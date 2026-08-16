<?php
declare(strict_types=1);

$lignesPanierAffichage = [];
$montantTotalPanier = 0;

foreach ($panier as $ligne) {
    $produit = null;

    foreach ($produits as $item) {
        if ($item->getId() === (int) $ligne['produitId']) {
            $produit = $item;
            break;
        }
    }

    if ($produit === null) {
        continue;
    }

    $quantite = (int) $ligne['quantite'];
    $sousTotal = $produit->getPrixVente() * $quantite;
    $montantTotalPanier += $sousTotal;

    $lignesPanierAffichage[] = [
        'produit' => $produit,
        'quantite' => $quantite,
        'sousTotal' => $sousTotal,
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager | Ventes / POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --panel-bg: rgba(22, 30, 49, 0.65);
            --border-color: rgba(45, 212, 191, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #2dd4bf;
            --accent-glow: rgba(45, 212, 191, 0.1);
            --success: #34d399;
            --danger: #f87171;
            --warning: #fbbf24;
            --font-family: 'Plus Jakarta Sans', sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: var(--font-family); min-height: 100vh; padding: 0; }
        .app-container { width: 100%; max-width: 100%; padding: 24px; }
        .navbar { display: flex; justify-content: space-between; align-items: center; background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color); padding: 16px 24px; border-radius: 20px; margin-bottom: 24px; backdrop-filter: blur(15px); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .nav-logo { font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .nav-logo span { color: var(--accent); }
        .nav-menu { display: flex; gap: 8px; }
        .nav-item { background: transparent; border: 1px solid transparent; color: var(--text-muted); padding: 10px 18px; border-radius: 12px; cursor: pointer; font-size: 13px; font-weight: 700; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .nav-item:hover, .nav-item.active { background: var(--accent-glow); color: var(--accent); border-color: var(--accent); }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge.payee { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge.non-payee { background: rgba(244, 63, 94, 0.1); color: var(--danger); }
        .panel-card { background: var(--panel-bg); border: 1px solid var(--border-color); backdrop-filter: blur(15px); border-radius: 24px; padding: 28px; box-shadow: 0 12px 35px rgba(0,0,0,0.3); margin-bottom: 24px; }
        .panel-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; border-left: 4px solid var(--accent); padding-left: 12px; display: flex; justify-content: space-between; align-items: center; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; position: relative; }
        .form-group label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; color: white; font-family: var(--font-family); outline: none; font-size: 13px; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 12px rgba(59, 130, 246, 0.1); }
        .btn-submit { background: linear-gradient(135deg, var(--accent) 0%, #0d9488 100%); color: #0b0f19; border: none; border-radius: 12px; padding: 14px 20px; font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; width: 100%; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(45, 212, 191, 0.3); }
        .btn-submit.btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; }
        .btn-submit:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }
        .debt-table { width: 100%; border-collapse: collapse; text-align: left; }
        .debt-table th { color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
        .debt-table td { padding: 14px 0; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 13px; }
        .btn-quick-action { background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-quick-action:hover { background: var(--accent-glow); border-color: var(--accent); color: var(--accent); }
        .btn-quick-action:disabled { opacity: 0.35; cursor: not-allowed; }
        .details-drawer { display: none; background: rgba(255,255,255,0.012); border: 1px solid rgba(255,255,255,0.03); border-radius: 16px; padding: 20px; margin-top: 10px; }
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
        .alert-danger { background: rgba(244, 63, 94, 0.1); border: 1px solid var(--danger); color: var(--danger); }
        .alert-success { background: rgba(52, 211, 153, 0.1); border: 1px solid var(--success); color: var(--success); }
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body>
    <div class="app-container">

        <div class="navbar">
            <div class="nav-logo"><span>📦</span> StoreManager Pro</div>
            <div class="nav-menu">
                <button class="nav-item" id="nav-dashboard" onclick="switchView('dashboard')">Tableau de Bord</button>
                <a class="nav-item active" href="/">Ventes / POS</a>
                <a class="nav-item" href="/dettes">Gestion Dettes</a>
                <button class="nav-item" id="nav-supplies" onclick="switchView('supplies')">Approvisionnements</button>
                <button class="nav-item" id="nav-catalog" onclick="switchView('catalog')">Produits & Tiers</button>
            </div>
            <div style="margin-left: auto; display: flex; align-items: center; gap: 14px;">
                <div style="text-align: right;">
                    <div id="current-user-role" style="font-size: 12px; font-weight: 800; color: var(--accent);">Admin Boutique</div>
                    <div style="font-size: 10px; color: var(--text-muted);">Session active</div>
                </div>
                <button type="button" class="btn-quick-action" onclick="logout()" style="border-color: var(--danger); color: var(--danger); background: rgba(248, 113, 113, 0.08); padding: 8px 12px;">Déconnexion 🚪</button>
            </div>
        </div>

        <?php if ($erreur !== null): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($succes !== null): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($succes, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- POS Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
                <div>
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">CA Encaissé Net</span>
                    <div style="font-size: 18px; font-weight: 800; margin-top: 4px;"><?= number_format($caEncaisseNet, 0, ',', ' ') ?> F</div>
                </div>
                <span style="font-size: 24px;">💰</span>
            </div>
            <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--danger);">
                <div>
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Encours Client Total</span>
                    <div style="font-size: 18px; font-weight: 800; margin-top: 4px;"><?= number_format($encoursClientTotal, 0, ',', ' ') ?> F</div>
                </div>
                <span style="font-size: 24px;">🛑</span>
            </div>
            <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--accent);">
                <div>
                    <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Commandes Enregistrées</span>
                    <div style="font-size: 18px; font-weight: 800; margin-top: 4px;"><?= count($ventes) ?> ventes</div>
                </div>
                <span style="font-size: 24px;">📊</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 600px 1fr; gap: 32px; align-items: start; margin-bottom: 32px;">

    <!-- Ticket de vente -->
    <div class="panel-card" style="margin-bottom: 0; padding: 24px; border: 1px solid rgba(59, 130, 246, 0.2); background: linear-gradient(180deg, rgba(17, 24, 43, 0.5) 0%, rgba(10, 15, 30, 0.3) 100%); position: sticky; top: 24px;">

        <div class="panel-title" style="border-left-color: var(--accent); display:flex; justify-content:space-between; align-items:center;">
            <span>🛒 Nouvelle Vente</span>
            <span style="font-size: 11px; font-weight: 600; color: var(--text-muted); background: rgba(255,255,255,0.03); padding: 4px 8px; border-radius: 6px;">
                Terminal POS
            </span>
        </div>

        <div class="form-group">
            <label for="client_id">Client Acheteur</label>

            <select
                name="client_id"
                id="client_id"
                class="form-control"
                form="vente-form"
                required
            >
                <?php foreach ($clients as $client): ?>
                    <option
                        value="<?= $client->getId() ?>"
                        <?= $client->getId() === $clientIdSelectionne ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars(
                            $client->getNom() . ' ' . $client->getPrenom(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                        (<?= htmlspecialchars(
                            $client->getTelephone(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <label style="font-size: 12px; font-weight: 700; color: var(--accent); display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
            Sélection des Articles
        </label>

        <!-- Étape 1 : ajout au panier -->
        <form method="POST" action="/panier" id="panier-form">

            <!-- Champ caché : recopie du client sélectionné plus haut (synchronisé en JS),
                 car ce formulaire est physiquement séparé du select #client_id. -->
            <input type="hidden" name="client_id" id="panier-client-id-hidden" value="<?= $clientIdSelectionne ?: '' ?>">

            <div style="display: grid; grid-template-columns: 1fr 90px 38px; gap: 10px; align-items: end; margin-bottom: 16px;">

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="produit_id" style="font-size: 10px;">
                        Article
                    </label>

                    <select
                        name="produit_id"
                        id="produit_id"
                        class="form-control"
                        style="padding: 10px; font-size: 12px;"
                        required
                    >
                        <?php foreach ($produits as $produit): ?>
                            <?php
                            $stock = $produit->getQuantiteStock();
                            $iconeStock = $stock <= 0
                                ? '🔴'
                                : ($stock <= 10 ? '🟡' : '🟢');
                            ?>

                            <option value="<?= $produit->getId() ?>">
                                <?= $iconeStock ?>
                                <?= htmlspecialchars(
                                    $produit->getNom(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                                (<?= $stock ?>)
                            </option>

                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="form-group" style="margin-bottom: 0;">
                    <label for="pos-qty" style="font-size: 10px;">
                        Qté
                    </label>

                    <input
                        type="number"
                        id="pos-qty"
                        name="quantite"
                        class="form-control"
                        value="1"
                        min="1"
                        style="padding: 10px; font-size: 12px;"
                        required
                    >
                </div>


                <button
                    type="submit"
                    class="btn-submit"
                    style="height: 38px; width: 38px; font-size: 18px; display: flex; justify-content: center; align-items: center; border-radius: 8px; padding: 0; flex-shrink: 0; min-width: 38px;"
                >
                    +
                </button>

            </div>

        </form>



        <table class="debt-table" style="font-size: 11px; margin-top: 16px;">

            <thead>
                <tr>
                    <th style="padding-bottom: 8px;">Produit</th>
                    <th style="padding-bottom: 8px;">Qté</th>
                    <th style="padding-bottom: 8px;">Total</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($lignesPanierAffichage === []): ?>

                    <tr>
                        <td
                            colspan="3"
                            style="text-align: center; color: var(--text-muted); padding: 16px 0; border-bottom: none;"
                        >
                            Panier vide. Ajoutez des articles.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($lignesPanierAffichage as $ligne): ?>

                        <?php $produit = $ligne['produit']; ?>

                        <tr>

                            <td style="padding: 8px 0; font-weight:700;">
                                <?= htmlspecialchars(
                                    $produit->getNom(),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td style="padding: 8px 0;">
                                <?= $ligne['quantite'] ?>
                            </td>

                            <td style="padding: 8px 0; font-weight:800; color:var(--accent);">
                                <?= number_format(
                                    $ligne['sousTotal'],
                                    0,
                                    ',',
                                    ' '
                                ) ?> F
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>


        <form method="POST" action="/ventes" id="vente-form">


            <div
                style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(30, 41, 59, 0.4) 100%); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 16px; padding: 14px; text-align: center; margin: 16px 0;"
            >

                <span
                    style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 1px; display: block; margin-bottom: 4px;"
                >
                    Montant Total Net à Payer
                </span>

                <div
                    style="font-size: 24px; font-weight: 900; color: #60a5fa; letter-spacing: -0.5px; font-family: monospace;"
                >
                    <?= number_format(
                        $montantTotalPanier,
                        0,
                        ',',
                        ' '
                    ) ?>

                    <span style="font-size: 14px; font-weight: 700;">
                        FCFA
                    </span>
                </div>

            </div>


            <div
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;"
            >

                <div class="form-group" style="margin-bottom: 0;">

                    <label
                        for="mode_reglement"
                        style="font-size: 10px;"
                    >
                        Règlement
                    </label>

                    <select
                        name="mode_reglement"
                        id="mode_reglement"
                        class="form-control"
                        style="padding: 10px; font-size: 12px;"
                        required
                    >
                        <option value="Wave">
                            Wave
                        </option>

                        <option value="Orange Money">
                            Orange Money
                        </option>

                        <option value="Especes">
                            Espèces
                        </option>
                    </select>

                </div>


                <div class="form-group" style="margin-bottom: 0;">

                    <label
                        for="montant_verse"
                        style="font-size: 10px;"
                    >
                        Versé (Avance)
                    </label>

                    <input
                        type="number"
                        name="montant_verse"
                        id="montant_verse"
                        class="form-control"
                        value="<?= (int) $montantTotalPanier ?>"
                        min="0"
                        style="padding: 10px; font-size: 12px;"
                        required
                    >

                </div>

            </div>



            <button
                type="submit"
                class="btn-submit btn-success"
                style="padding: 14px; font-weight: 800; font-size: 13px;"
                <?= $panier === [] ? 'disabled' : '' ?>
            >
                Valider la Vente
            </button>

        </form>

    </div>
        <!-- Registre des ventes -->
            <div class="panel-card" style="margin-bottom: 0;">
                <div class="panel-title">Registre Général des Ventes & Commandes</div>
                <table class="debt-table">
                    <thead>
                        <tr><th>ID</th><th>Client</th><th>Total Facture</th><th>Règlement</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($ventes === []): ?>
                            <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 16px 0;">Aucune vente enregistrée.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ventes as $vente): ?>
                                <?php $commande = $vente['commande']; $client = $vente['client']; ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--text-muted);">#CMD-<?= $commande->getId() ?></td>
                                    <td style="font-weight: 700;">
                                        <?php if ($client !== null): ?>
                                            <?= htmlspecialchars($client->getPrenom() . ' ' . $client->getNom(), ENT_QUOTES, 'UTF-8') ?>
                                            <div style="font-size:11px; color:var(--text-muted); font-weight:normal;">Tél : <?= htmlspecialchars($client->getTelephone(), ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php else: ?>
                                            Client inconnu
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: 800; color: var(--accent);"><?= number_format($commande->getMontantTotal(), 0, ',', ' ') ?> F</td>
                                    <td>
                                        <?php if ($commande->getStatut() === 'CREDIT'): ?>
                                            <span class="badge non-payee">CRÉDIT</span>
                                        <?php else: ?>
                                            <span class="badge payee">COMPTANT (<?= htmlspecialchars($commande->getModeReglement(), ENT_QUOTES, 'UTF-8') ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-quick-action" onclick="toggleDetails('vente-lignes-<?= $commande->getId() ?>')">Lignes</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="padding: 0; border: none;">
                                        <div class="details-drawer" id="vente-lignes-<?= $commande->getId() ?>">
                                            <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Articles de la commande #CMD-<?= $commande->getId() ?> :</div>
                                            <table class="debt-table" style="font-size: 11px;">
                                                <thead><tr><th>Produit</th><th>Quantité</th><th>Prix Unit.</th><th>Sous-total</th></tr></thead>
                                                <tbody>
                                                    <?php foreach ($vente['articles'] as $article): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars((string) $article['produit_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td><?= (int) $article['quantite'] ?></td>
                                                            <td><?= number_format((float) $article['prix_unitaire'], 0, ',', ' ') ?> F</td>
                                                            <td style="font-weight: 700;"><?= number_format((int) $article['quantite'] * (float) $article['prix_unitaire'], 0, ',', ' ') ?> F</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

</div>

        
        </div>
    </div>

    <script>
        // Garde le champ caché du formulaire "panier" synchronisé avec le
        // <select> client (qui appartient lui à #vente-form), pour que le
        // client choisi survive au rechargement de page déclenché par le "+".
        (function () {
            const selectClient = document.getElementById('client_id');
            const hiddenClient = document.getElementById('panier-client-id-hidden');
            if (!selectClient || !hiddenClient) return;

            hiddenClient.value = selectClient.value;
            selectClient.addEventListener('change', function () {
                hiddenClient.value = selectClient.value;
            });
        })();

        function toggleDetails(panelId) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const isVisible = window.getComputedStyle(panel).display !== 'none';

            document.querySelectorAll('.details-drawer').forEach(dr => {
                if (dr.closest('tr') === panel.closest('tr') && dr.id !== panelId) {
                    dr.style.display = 'none';
                }
            });

            panel.style.display = isVisible ? 'none' : 'block';
        }
    </script>
</body>
</html>