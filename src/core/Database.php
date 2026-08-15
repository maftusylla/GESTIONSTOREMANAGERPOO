<?php

declare(strict_types=1);

final class Database
{
    private static ?Database $instance = null;

    private PDO $pdo;

    private string $driver;

    private function __construct()
    {
        try {
          
            $this->pdo    = $this->connectPostgreSQL();
            $this->driver = 'pgsql';
        } catch (PDOException $e) {
            error_log(
                '[Database] Connexion PostgreSQL impossible (' . $e->getMessage() . '). '
                . 'Fallback automatique sur SQLite (erp.db).'
            );

            try {
                $this->pdo    = $this->connectSQLite();
                $this->driver = 'sqlite';
            } catch (PDOException $e2) {
                
                error_log('[Database] Fallback SQLite également en échec : ' . $e2->getMessage());
                throw new RuntimeException(
                    'Impossible de se connecter à la base de données (PostgreSQL et SQLite ont tous deux échoué).',
                    0,
                    $e2
                );
            }
        }

        $this->configurePDO();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    private function configurePDO(): void
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($this->driver === 'pgsql') {
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
    }

    private function connectPostgreSQL(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('DB_NAME') ?: 'storemanagerpro';
        $user = getenv('DB_USER') ?: 'postgres';
        $password = getenv('DB_PASSWORD') ?: 'PASSWORD';

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        return new PDO($dsn, $user, $password);
    }

    private function connectSQLite(): PDO
    {
        $racineProjet = dirname(__DIR__, 2);
        $dbFile = $racineProjet . '/erp.db';
        $schemaFile = $racineProjet . '/schema_sqlite.sql';
        $doitEtreInitialisee = !file_exists($dbFile);

        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->exec('PRAGMA foreign_keys = ON;');

        if ($doitEtreInitialisee && file_exists($schemaFile)) {
            $schema = file_get_contents($schemaFile);
            if ($schema === false) {
                throw new RuntimeException('Impossible de lire schema_sqlite.sql.');
            }
            $pdo->exec($schema);
        }

        return $pdo;
    }

    public function query(string $sql, bool $single = true): array
    {
        $statement = $this->pdo->query($sql);
        $resultat = $single ? $statement->fetch() : $statement->fetchAll();
        return $resultat === false ? [] : $resultat;
    }

    public function prepare(string $sql, array $datas = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($datas);
        return $statement;
    }

    public function executeQuery(string $sql, array $datas = [], bool $single = true): array
    {
        $statement = $this->prepare($sql, $datas);
        $resultat = $single ? $statement->fetch() : $statement->fetchAll();
        return $resultat === false ? [] : $resultat;
    }

    public function executeUpdate(string $sql, array $datas = []): int
    {
        $statement = $this->prepare($sql, $datas);
        return $statement->rowCount();
    }

    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $resultat = $callback($this);
            $this->pdo->commit();
            return $resultat;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Impossible de désérialiser le Singleton Database.');
    }
}