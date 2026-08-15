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

            $this->pdo    = $this->connectSQLite();
            $this->driver = 'sqlite';
        }
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


    private function connectPostgreSQL(): PDO
    {
        $host     = getenv('DB_HOST') ?: '127.0.0.1';
        $port     = getenv('DB_PORT') ?: '5432';
        $dbname   = getenv('DB_NAME') ?: 'gestionStoreManagerPro';
        $user     = getenv('DB_USER') ?: 'postgres';
        $password = getenv('DB_PASSWORD') ?: 'PASSWORD';

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, 
        ]);
    }

 
    private function connectSQLite(): PDO
    {
        $racineProjet = dirname(__DIR__, 2);
        $dbFile       = $racineProjet . '/erp.db';
        $schemaFile   = $racineProjet . '/schema_sqlite.sql';

        $doitEtreInitialisee = !file_exists($dbFile);

        $pdo = new PDO('sqlite:' . $dbFile, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec('PRAGMA foreign_keys = ON;');

        if ($doitEtreInitialisee && file_exists($schemaFile)) {
            $pdo->exec((string) file_get_contents($schemaFile));
        }

        return $pdo;
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new \RuntimeException('Impossible de désérialiser le Singleton Database.');
    }
}