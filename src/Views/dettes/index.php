<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StoreManager | Gestion des Dettes</title>
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
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            font-family: var(--font-family);
            min-height: 100vh;
            padding: 0;
        }
        .app-container { width: 100%; max-width: 100%; padding: 24px; }
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color);
            padding: 16px 24px; border-radius: 20px; margin-bottom: 24px;
            backdrop-filter: blur(15px); box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .nav-logo { font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .nav-logo span { color: var(--accent); }
        .nav-menu { display: flex; gap: 8px; }
        .nav-item {
            background: transparent; border: 1px solid transparent; color: var(--text-muted);
            padding: 10px 18px; border-radius: 12px; cursor: pointer; font-size: 13px;
            font-weight: 700; transition: all 0.3s; text-decoration: none; display: inline-block;
        }
        .nav-item:hover, .nav-item.active { background: var(--accent-glow); color: var(--accent); border-color: var(--accent); }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge.payee { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge.non-payee { background: rgba(244, 63, 94, 0.1); color: var(--danger); }
        .panel-card { background: var(--panel-bg); border: 1px solid var(--border-color); backdrop-filter: blur(15px); border-radius: 24px; padding: 28px; box-shadow: 0 12px 35px rgba(0,0,0,0.3); margin-bottom: 24px; }
        .panel-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; border-left: 4px solid var(--accent); padding-left: 12px; display: flex; justify-content: space-between; align-items: center; }
        .form-control { background: rgba(8, 12, 24, 0.7); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; color: white; font-family: var(--font-family); outline: none; font-size: 13px; }
        .btn-submit { background: linear-gradient(135deg, var(--accent) 0%, #0d9488 100%); color: #0b0f19; border: none; border-radius: 12px; padding: 14px 20px; font-weight: 800; font-size: 13px; text-transform: uppercase; cursor: pointer; }
        .btn-submit.btn-success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; }
        .debt-table { width: 100%; border-collapse: collapse; text-align: left; }
        .debt-table th { color: var(--text-muted); font-size: 11px; font-weight: 700; text-transform: uppercase; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
        .debt-table td { padding: 14px 0; border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 13px; }
        .btn-quick-action { background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 8px; padding: 6px 12px; font-size: 11px; font-weight: 700; cursor: pointer; }
        .btn-quick-action:hover { background: var(--accent-glow); border-color: var(--accent); color: var(--accent); }
        .details-drawer { display: none; background: rgba(255,255,255,0.012); border: 1px solid rgba(255,255,255,0.03); border-radius: 16px; padding: 20px; margin-top: 10px; }
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600; }
        .alert-danger { background: rgba(244, 63, 94, 0.1); border: 1px solid var(--danger); color: var(--danger); }
        .alert-success { background: rgba(52, 211, 153, 0.1); border: 1px solid var(--success); color: var(--success); }
    </style>
</head>
<body>
    <div class="navbar">
            <div class="nav-logo">
                <span>📦</span> StoreManager Pro
            </div>
            <div class="nav-menu">
                <button class="nav-item" id="nav-dashboard" onclick="switchView('dashboard')">Tableau de Bord</button>
                 <a class="nav-item" href="/">Ventes / POS</a>
              <a class="nav-item active" href="/dettes">Gestion Dettes</a>
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

<div class="app-container">

   
    <?php if ($erreur !== null): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($succes !== null): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--danger);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Créances Actives</span>
                <div style="font-size: 18px; font-weight: 800;"><?= number_format($creancesActives, 0, ',', ' ') ?> F</div>
            </div>
            <span style="font-size: 24px;">💸</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--warning);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Clients Débiteurs</span>
                <div style="font-size: 18px; font-weight: 800;"><?= $nombreClientsDebiteurs ?> client(s)</div>
            </div>
            <span style="font-size: 24px;">👥</span>
        </div>
        <div class="panel-card" style="padding: 16px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
            <div>
                <span style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Recouvrements</span>
                <div style="font-size: 18px; font-weight: 800;"><?= number_format($totalRecouvrements, 0, ',', ' ') ?> F</div>
            </div>
            <span style="font-size: 24px;">📈</span>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-title"><span>Registre des Dettes</span></div>

        <table class="debt-table">
            <thead>
                <tr>
                    <th>ID Dette</th>
                    <th>Date Création</th>
                    <th>Client</th>
                    <th>Montant Initial</th>
                    <th>Montant Payé</th>
                    <th>Reste Dû</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignesDettes as $ligne): ?>
                    <?php
                        $dette = $ligne['dette'];
                        $client = $ligne['client'];
                        $montantPaye = $dette->getMontantInitial() - $dette->getMontantRestant();
                        $estSoldee = $dette->estSoldee();
                    ?>
                    <tr id="debt-row-<?= $dette->getId() ?>">
                        <td style="font-weight: 700; color: var(--text-muted);">
                            #DT-<?= $dette->getId() ?>
                            <span style="font-size: 10px; display: block; font-weight: normal;">#CMD-<?= $dette->getCommandeId() ?></span>
                        </td>
                        <td style="font-size: 12px;"><?= $dette->getDateCreation()->format('d M Y H:i') ?></td>
                        <td style="font-weight: 700;">
                            <?= $client !== null ? htmlspecialchars($client->getPrenom() . ' ' . $client->getNom()) : '—' ?>
                            <?php if ($client !== null): ?>
                                <div style="font-size:11px; color:var(--text-muted); font-weight:normal;">Tél : <?= htmlspecialchars($client->getTelephone()) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 700;"><?= number_format($dette->getMontantInitial(), 0, ',', ' ') ?> F</td>
                        <td style="font-weight: 700; color: var(--success);"><?= number_format($montantPaye, 0, ',', ' ') ?> F</td>
                        <td style="color: var(--danger); font-weight: 800;"><?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> F</td>
                        <td>
                            <span class="badge <?= $estSoldee ? 'payee' : 'non-payee' ?>">
                                <?= htmlspecialchars($dette->getStatut()) ?>
                            </span>
                        </td>
                        <td style="display: flex; gap: 6px;">
                            <button type="button" class="btn-quick-action" onclick="toggleDetails('debt-lines-<?= $dette->getId() ?>')">Articles</button>
                            <button type="button" class="btn-quick-action" style="border-color: var(--accent); color: var(--accent);" onclick="toggleDetails('debt-details-<?= $dette->getId() ?>')">💳 Paiements</button>
                            <?php if (!$estSoldee): ?>
                                <button type="button" class="btn-quick-action" style="border-color: var(--warning); color: var(--warning);" onclick="toggleDetails('debt-repay-<?= $dette->getId() ?>')">Rembourser</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" style="padding: 0; border: none;">

                            <div class="details-drawer" id="debt-lines-<?= $dette->getId() ?>">
                                <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Articles de la commande #CMD-<?= $dette->getCommandeId() ?> :</div>
                                <table class="debt-table" style="font-size: 11px;">
                                    <thead><tr><th>Produit</th><th>Quantité</th><th>Prix Unit.</th><th>Sous-total</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($ligne['articles'] as $article): ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string) $article['produit_nom']) ?></td>
                                                <td><?= (int) $article['quantite'] ?></td>
                                                <td><?= number_format((float) $article['prix_unitaire'], 0, ',', ' ') ?> F</td>
                                                <td style="font-weight: 700;"><?= number_format((int) $article['quantite'] * (float) $article['prix_unitaire'], 0, ',', ' ') ?> F</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="details-drawer" id="debt-details-<?= $dette->getId() ?>">
                                <div style="font-weight: 700; font-size: 12px; color: var(--accent); margin-bottom: 8px;">Paiements enregistrés :</div>
                                <?php if ($ligne['paiements'] === []): ?>
                                    <p style="font-size: 12px; color: var(--text-muted);">Aucun paiement enregistré pour l'instant.</p>
                                <?php else: ?>
                                    <table class="debt-table" style="font-size: 11px;">
                                        <thead><tr><th>Date</th><th>Versement</th><th>Mode</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($ligne['paiements'] as $paiement): ?>
                                                <tr>
                                                    <td><?= $paiement->getDatePaiement()->format('d M Y H:i') ?></td>
                                                    <td style="font-weight: 700; color: var(--accent);"><?= number_format($paiement->getMontant(), 0, ',', ' ') ?> F</td>
                                                    <td><?= htmlspecialchars($paiement->getModePaiement()) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>

                            <?php if (!$estSoldee): ?>
                                <div class="details-drawer" id="debt-repay-<?= $dette->getId() ?>" style="border: 1px solid rgba(45,212,191,0.25);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                                        <span style="font-weight: 800; font-size: 13px;">
                                            💳 Nouveau Remboursement — <span style="color: var(--accent);"><?= $client !== null ? htmlspecialchars($client->getPrenom() . ' ' . $client->getNom()) : '—' ?></span>
                                        </span>
                                        <div style="background: rgba(244,63,94,0.12); border: 1px solid rgba(244,63,94,0.3); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; color: var(--danger);">
                                            Reste dû : <?= number_format($dette->getMontantRestant(), 0, ',', ' ') ?> FCFA
                                        </div>
                                    </div>

                                    <form method="POST" action="/dettes/payer" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">

                                        <input type="hidden" name="dette_id" value="<?= $dette->getId() ?>">

                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; font-weight: 700;">Montant du Versement (FCFA)</label>
                                            <input type="number" name="montant_verse" class="form-control" max="<?= $dette->getMontantRestant() ?>" min="1" step="1" required style="width: 100%;">
                                        </div>

                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="font-size: 10px; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; font-weight: 700;">Canal de Paiement</label>
                                            <select name="mode_paiement" class="form-control" style="width: 100%;" required>
                                                <option value="Especes">💵 Espèces</option>
                                                <option value="Wave">🌊 Wave</option>
                                                <option value="Orange Money">🟠 Orange Money</option>
                                            </select>
                                        </div>

                                        <div>
                                            <button type="submit" class="btn-submit btn-success">✓ Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($lignesDettes === []): ?>
                    <tr><td colspan="8" style="text-align: center; padding: 24px; color: var(--text-muted);">Aucune dette enregistrée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

  <script>
        let activeInputId = null;
        let currentUserRole = null;

        const rolePermissions = {
            admin: { name: "👑 Admin Boutique", allowedViews: ['dashboard', 'pos', 'dettes', 'supplies', 'catalog'], defaultView: 'dashboard' },
            vente: { name: "🛒 Chargé de Vente", allowedViews: ['pos', 'dettes'], defaultView: 'pos' },
            stock: { name: "📦 Chargé de Stock", allowedViews: ['supplies', 'catalog'], defaultView: 'supplies' },
            inventaire: { name: "📋 Inventaire", allowedViews: ['catalog'], defaultView: 'catalog' }
        };

        function updateProfileInfoHint() {
            const roleKey = document.getElementById("login-role-select").value;
            const hintBox = document.getElementById("role-desc-hint");
            if (roleKey === 'admin') {
                hintBox.innerHTML = "💡 <strong>Admin Boutique</strong> : Contrôle total sur la comptabilité, ventes, dettes, approvisionnements et paramétrage.";
            } else if (roleKey === 'vente') {
                hintBox.innerHTML = "💡 <strong>Chargé de Vente</strong> : Accès restreint à la caisse tactile POS et au registre de suivi des dettes clients.";
            } else if (roleKey === 'stock') {
                hintBox.innerHTML = "💡 <strong>Chargé de Stock</strong> : Gestion des approvisionnements, réception de marchandises et catalogue produits/fournisseurs.";
            } else if (roleKey === 'inventaire') {
                hintBox.innerHTML = "💡 <strong>Inventaire</strong> : Mode consultation et comptage des stocks et répertoires tiers.";
            }
        }

        function selectQuickProfile(roleKey, email, roleName) {
            document.getElementById("login-role-select").value = roleKey;
            const emailInput = document.getElementById("login-email");
            if (emailInput) emailInput.value = email;

            document.querySelectorAll(".quick-profile-card").forEach(card => {
                card.style.borderColor = "rgba(255, 255, 255, 0.08)";
                card.style.borderWidth = "1px";
                card.style.background = "rgba(22, 30, 49, 0.4)";
                card.classList.remove("active");
            });

            const activeCard = document.getElementById("profile-card-" + roleKey);
            if (activeCard) {
                activeCard.style.borderColor = "var(--accent)";
                activeCard.style.borderWidth = "2px";
                activeCard.style.background = "rgba(22, 30, 49, 0.75)";
                activeCard.classList.add("active");
            }
        }

        function handleLogin(event) {
            event.preventDefault();
            const selectedRole = document.getElementById("login-role-select").value;
            currentUserRole = selectedRole;
            localStorage.setItem("erp_logged_role", selectedRole);
            
            document.getElementById("login-screen").style.display = "none";
            applyRolePermissions();
        }

        function logout() {
            localStorage.removeItem("erp_logged_role");
            currentUserRole = null;
            document.getElementById("login-screen").style.display = "flex";
        }

        function applyRolePermissions() {
            const role = localStorage.getItem("erp_logged_role") || currentUserRole;
            if (!role || !rolePermissions[role]) {
                document.getElementById("login-screen").style.display = "flex";
                return;
            }

            document.getElementById("login-screen").style.display = "none";
            const roleConfig = rolePermissions[role];
            document.getElementById("current-user-role").innerText = roleConfig.name;

            // Afficher ou masquer les éléments du menu
            const views = ['dashboard', 'pos', 'dettes', 'supplies', 'catalog'];
            views.forEach(v => {
                const navBtn = document.getElementById("nav-" + v);
                if (navBtn) {
                    if (roleConfig.allowedViews.includes(v)) {
                        navBtn.style.display = "inline-block";
                    } else {
                        navBtn.style.display = "none";
                    }
                }
            });

            // Basculer vers la vue autorisée par défaut ou sauvegardée
            let currentView = localStorage.getItem("active_erp_view");
            if (!roleConfig.allowedViews.includes(currentView)) {
                currentView = roleConfig.defaultView;
            }
            switchView(currentView);
        }

        function switchView(viewId) {
            const role = localStorage.getItem("erp_logged_role");
            if (role && rolePermissions[role] && !rolePermissions[role].allowedViews.includes(viewId)) {
                alert("Accès non autorisé pour le profil " + rolePermissions[role].name);
                return;
            }

            document.querySelectorAll(".nav-menu .nav-item").forEach(item => item.classList.remove("active"));
            const activeNav = document.getElementById("nav-" + viewId);
            if (activeNav) activeNav.classList.add("active");

            document.querySelectorAll(".view-section").forEach(sec => sec.classList.remove("active-view"));
            const targetSection = document.getElementById("view-" + viewId);
            if (targetSection) targetSection.classList.add("active-view");

            localStorage.setItem("active_erp_view", viewId);
            hideKeypad();
        }

        function switchSupplyTab(tabName) {
            document.getElementById("supply-tab-btn-create").classList.remove("active");
            document.getElementById("supply-tab-btn-supplier").classList.remove("active");
            document.getElementById("supply-panel-create").style.display = "none";
            document.getElementById("supply-panel-supplier").style.display = "none";
            document.getElementById("supply-panel-receive-confirm").style.display = "none";

            if (tabName === 'create') {
                document.getElementById("supply-tab-btn-create").classList.add("active");
                document.getElementById("supply-panel-create").style.display = "block";
            } else if (tabName === 'supplier') {
                document.getElementById("supply-tab-btn-supplier").classList.add("active");
                document.getElementById("supply-panel-supplier").style.display = "block";
            }
            hideKeypad();
        }

        function startReception(appId, blRef, items) {
            document.getElementById("supply-panel-create").style.display = "none";
            document.getElementById("supply-panel-supplier").style.display = "none";
            document.getElementById("supply-panel-receive-confirm").style.display = "block";

            document.getElementById("receive-bl-ref").innerText = blRef;
            document.getElementById("receive-app-id").value = appId;

            const container = document.getElementById("receive-items-container");
            container.innerHTML = "";

            items.forEach(item => {
                container.innerHTML += `
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 12px;">
                        <div style="font-weight: 700; font-size: 13px; margin-bottom: 8px; color: var(--text-main);">${item.nom}</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; align-items: center;">
                            <div style="font-size: 11px; color: var(--text-muted);">
                                Attendu : <strong>${item.quantite}</strong>
                            </div>
                            <div>
                                <label style="font-size: 9px; color: var(--text-muted); display: block; margin-bottom: 4px; text-transform: uppercase;">Qté Reçue</label>
                                <input type="number" name="quantites_livrees[${item.produit_id}]" class="form-control" value="${item.quantite}" min="0" required style="padding: 8px; font-size: 12px;">
                            </div>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById("supply-tab-btn-create").classList.remove("active");
            document.getElementById("supply-tab-btn-supplier").classList.remove("active");
            hideKeypad();
        }

        function cancelReception() {
            document.getElementById("supply-panel-receive-confirm").style.display = "none";
            document.getElementById("supply-panel-create").style.display = "block";
            document.getElementById("supply-tab-btn-create").classList.add("active");
        }

        function switchDashLeftTab(tabName) {
            document.getElementById("dash-left-tab-sales").classList.remove("active");
            document.getElementById("dash-left-tab-debts").classList.remove("active");
            document.getElementById("dash-left-tab-ruptures").classList.remove("active");
            document.getElementById("dash-left-panel-sales").style.display = "none";
            document.getElementById("dash-left-panel-debts").style.display = "none";
            document.getElementById("dash-left-panel-ruptures").style.display = "none";

            if (tabName === 'sales') {
                document.getElementById("dash-left-tab-sales").classList.add("active");
                document.getElementById("dash-left-panel-sales").style.display = "block";
            } else if (tabName === 'debts') {
                document.getElementById("dash-left-tab-debts").classList.add("active");
                document.getElementById("dash-left-panel-debts").style.display = "block";
            } else if (tabName === 'ruptures') {
                document.getElementById("dash-left-tab-ruptures").classList.add("active");
                document.getElementById("dash-left-panel-ruptures").style.display = "block";
            }
        }

        function switchDashRightTab(tabName) {
            document.getElementById("dash-right-tab-supplies").classList.remove("active");
            document.getElementById("dash-right-tab-debtors").classList.remove("active");
            document.getElementById("dash-right-tab-fournisseurs").classList.remove("active");
            document.getElementById("dash-right-panel-supplies").style.display = "none";
            document.getElementById("dash-right-panel-debtors").style.display = "none";
            document.getElementById("dash-right-panel-fournisseurs").style.display = "none";

            if (tabName === 'supplies') {
                document.getElementById("dash-right-tab-supplies").classList.add("active");
                document.getElementById("dash-right-panel-supplies").style.display = "block";
            } else if (tabName === 'debtors') {
                document.getElementById("dash-right-tab-debtors").classList.add("active");
                document.getElementById("dash-right-panel-debtors").style.display = "block";
            } else if (tabName === 'fournisseurs') {
                document.getElementById("dash-right-tab-fournisseurs").classList.add("active");
                document.getElementById("dash-right-panel-fournisseurs").style.display = "block";
            }
        }

        function approvisionnerProduit(productId) {
            switchView('supplies');
            switchSupplyTab('create');
            const select = document.getElementById("supply-item-select");
            if (select) {
                select.value = productId;
            }
            const costInput = document.getElementById("supply-cost");
            if (costInput) {
                costInput.focus();
            }
        }

        function setRepayAmount(debtId, amount) {
            const input = document.getElementById("repay-input-" + debtId);
            if (input) {
                input.value = amount;
                input.focus();
            }
        }

        function toggleDetails(panelId) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const isVisible = window.getComputedStyle(panel).display !== 'none';
            panel.style.display = isVisible ? 'none' : 'block';

            // Find parent tr and toggle it too!
            const parentRow = panel.closest('tr');
            if (parentRow) {
                // If this is Dettes, hide other drawers in same tr when opening one
                if (panelId.includes('debt-')) {
                    const drawers = Array.from(parentRow.querySelectorAll('.details-drawer'));
                    drawers.forEach(dr => {
                        if (dr.id !== panelId) dr.style.display = 'none';
                    });
                }

                // If at least one details-drawer inside this row is block, show the parent row, else hide it.
                const drawers = Array.from(parentRow.querySelectorAll('.details-drawer'));
                const anyOpen = drawers.some(dr => window.getComputedStyle(dr).display !== 'none');
                parentRow.style.display = anyOpen ? '' : 'none';
            }
        }

        // Initialize view
        document.addEventListener("DOMContentLoaded", () => {
            applyRolePermissions();
            updateClientLimitInfo();

            // Initialisation de la pagination sur tous les tableaux principaux
            initPaginatedTable("debts-main-table", 10);
            initPaginatedTable("orders-main-table", 10);
            initPaginatedTable("supplies-main-table", 10);
            initPaginatedTable("catalog-main-table", 10);
            initPaginatedTable("clients-main-table", 10);

            // Auto dismiss toast after 4 seconds
            const mainToast = document.getElementById("main-toast");
            if (mainToast) {
                setTimeout(() => {
                    mainToast.style.animation = "slideIn 0.3s ease reverse forwards";
                    setTimeout(() => mainToast.remove(), 300);
                }, 4000);
            }
        });

        function switchCatalogTab(tabName) {
            document.getElementById("catalog-tab-btn-products").classList.remove("active");
            document.getElementById("catalog-tab-btn-clients").classList.remove("active");
            const suppBtn = document.getElementById("catalog-tab-btn-suppliers");
            if (suppBtn) suppBtn.classList.remove("active");

            document.getElementById("catalog-panel-products").style.display = "none";
            document.getElementById("catalog-panel-clients").style.display = "none";
            const suppPanel = document.getElementById("catalog-panel-suppliers");
            if (suppPanel) suppPanel.style.display = "none";

            if (tabName === 'products') {
                document.getElementById("catalog-tab-btn-products").classList.add("active");
                document.getElementById("catalog-panel-products").style.display = "grid";
            } else if (tabName === 'clients') {
                document.getElementById("catalog-tab-btn-clients").classList.add("active");
                document.getElementById("catalog-panel-clients").style.display = "grid";
            } else if (tabName === 'suppliers') {
                if (suppBtn) suppBtn.classList.add("active");
                if (suppPanel) suppPanel.style.display = "grid";
            }
            hideKeypad();
        }

        // POS Virtual tactile keyboard
        function showKeypad(inputId) {
            activeInputId = inputId;
            // Hide all keypads first
            document.getElementById("pos-keypad").style.display = "none";
            document.getElementById("payment-keypad").style.display = "none";
            const supplyKeypad = document.getElementById("supply-keypad");
            if (supplyKeypad) supplyKeypad.style.display = "none";

            if (inputId === 'pos-qty' || inputId === 'pos-montant-verse') {
                document.getElementById("pos-keypad").style.display = "grid";
            } else if (inputId === 'payment-amount') {
                document.getElementById("payment-keypad").style.display = "grid";
            } else if ((inputId === 'supply-qty' || inputId === 'supply-cost') && supplyKeypad) {
                supplyKeypad.style.display = "grid";
            }
        }

        function pressKey(key) {
            if (!activeInputId) return;
            const input = document.getElementById(activeInputId);

            if (key === 'C') {
                input.value = "";
            } else {
                input.value = (input.value === "1" || input.value === "0") && activeInputId === 'pos-qty' ? key : input.value + key;
            }
        }

        function hideKeypad() {
            document.getElementById("pos-keypad").style.display = "none";
            document.getElementById("payment-keypad").style.display = "none";
            const supplyKeypad = document.getElementById("supply-keypad");
            if (supplyKeypad) supplyKeypad.style.display = "none";
            activeInputId = null;
        }

        // Live autocomplete search filters
        function filterDebtsTable() {
            const query = document.getElementById("debt-search").value.toLowerCase();
            const rows = document.querySelectorAll("#debts-main-table tbody > tr");

            rows.forEach(row => {
                const cell = row.querySelector("td");
                if (cell && cell.getAttribute("colspan") !== null) return;

                const searchVal = row.getAttribute("data-client-name");
                if (searchVal) {
                    row.style.display = searchVal.includes(query) ? "" : "none";
                }
            });

            const table = document.getElementById("debts-main-table");
            if (table && table.updatePagination) {
                table.updatePagination();
            }
        }

        function filterProductsTable() {
            const query = document.getElementById("catalog-search").value.toLowerCase();
            const rows = document.querySelectorAll("#catalog-main-table tbody > tr");

            rows.forEach(row => {
                const searchVal = row.getAttribute("data-product-name");
                if (searchVal) {
                    row.style.display = searchVal.includes(query) ? "" : "none";
                }
            });

            const table = document.getElementById("catalog-main-table");
            if (table && table.updatePagination) {
                table.updatePagination();
            }
        }

        // POS Shopping Cart logic
        const cart = [];

        function addToCart(event) {
            event.preventDefault();
            const select = document.getElementById("pos-item-select");
            const price = parseFloat(select.options[select.selectedIndex].getAttribute("data-price"));
            const name = select.options[select.selectedIndex].getAttribute("data-name");
            const stock = parseInt(select.options[select.selectedIndex].getAttribute("data-stock"));
            const id = select.value;
            const qty = parseInt(document.getElementById("pos-qty").value);

            if (qty <= 0) return;

            if (qty > stock) {
                alert(`Stock insuffisant pour ${name} (${stock} disponible) !`);
                return;
            }

            const existing = cart.find(item => item.id === id);
            if (existing) {
                if (existing.qty + qty > stock) {
                    alert(`Stock insuffisant (${stock} disponible) !`);
                    return;
                }
                existing.qty += qty;
                existing.total = existing.qty * price;
            } else {
                cart.push({ id, name, price, qty, total: qty * price });
            }

            renderCart();
            hideKeypad();
        }

        function removeCartItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function renderCart() {
            const body = document.getElementById("cart-rows");
            const textDisplay = document.getElementById("montant_total_display_text");
            const valueInput = document.getElementById("montant_total_display");
            const hiddenInputs = document.getElementById("hidden-cart-inputs");

            if (cart.length === 0) {
                body.innerHTML = `
                    <tr id="empty-cart-row">
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 16px 0; border-bottom: none;">Panier vide. Ajoutez des articles.</td>
                    </tr>
                `;
                textDisplay.innerText = "0";
                valueInput.value = 0;
                hiddenInputs.innerHTML = "";
                document.getElementById("pos-montant-verse").value = 0;
                return;
            }

            body.innerHTML = "";
            hiddenInputs.innerHTML = "";
            let overallTotal = 0;

            cart.forEach((item, index) => {
                overallTotal += item.total;
                body.innerHTML += `
                    <tr>
                        <td style="padding: 8px 0; font-weight:700;">${item.name}</td>
                        <td style="padding: 8px 0;">${item.qty}</td>
                        <td style="padding: 8px 0; font-weight:800; color:var(--accent);">${new Intl.NumberFormat('fr-FR').format(item.total)} F</td>
                        <td style="padding: 8px 0; text-align:right;">
                            <button type="button" onclick="removeCartItem(${index})" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:14px;">🗑️</button>
                        </td>
                    </tr>
                `;
                hiddenInputs.innerHTML += `
                    <input type="hidden" name="product_ids[]" value="${item.id}">
                    <input type="hidden" name="product_qtys[]" value="${item.qty}">
                `;
            });

            textDisplay.innerText = new Intl.NumberFormat('fr-FR').format(overallTotal);
            valueInput.value = overallTotal;
            
            // Par défaut, le paiement est au comptant (montant versé = montant total)
            document.getElementById("pos-montant-verse").value = overallTotal;
        }

        function updateClientLimitInfo() {
            const select = document.getElementById("client-select");
            if (!select || select.selectedIndex < 0) return;
            const opt = select.options[select.selectedIndex];
            if (!opt) return;
            const limit = parseFloat(opt.getAttribute("data-limit"));
            document.getElementById("credit-limit-info").innerText = `Limite de crédit autorisée : ${new Intl.NumberFormat('fr-FR').format(limit)} FCFA`;
        }

        // Remboursements actions
        function openPaymentPanel(detteId, resteDu, clientName) {
            document.getElementById("payment-debt-id").value = detteId;
            document.getElementById("payment-client-name").value = clientName;
            document.getElementById("payment-max-limit").value = new Intl.NumberFormat('fr-FR').format(resteDu) + " F";
            document.getElementById("payment-amount").max = resteDu;
            document.getElementById("payment-amount").value = resteDu;
            
            // Toggle form views
            document.getElementById("action-placeholder").style.display = "none";
            document.getElementById("sms-alert-form-wrapper").style.display = "none";
            document.getElementById("remboursement-form-wrapper").style.display = "block";
            
            highlightDebtRow(detteId);
            showKeypad('payment-amount');
        }

        // Alert SMS triggers
        function generateSMSAlert(detteId, prenom, reste, telephone) {
            const area = document.getElementById("sms-text-area");
            area.value = `RAPPEL DE REMBOURSEMENT\n\nCher(e) ${prenom},\nNous vous rappelons qu'un solde restant de ${new Intl.NumberFormat('fr-FR').format(reste)} FCFA est en attente de régularisation sur votre compte StoreManager.\nMerci de procéder au règlement via Wave, Orange Money ou Espèces.\n\nContact : ${telephone}.`;

            // Toggle form views
            document.getElementById("action-placeholder").style.display = "none";
            document.getElementById("remboursement-form-wrapper").style.display = "none";
            document.getElementById("sms-alert-form-wrapper").style.display = "block";
            
            highlightDebtRow(detteId);
            hideKeypad();
        }

        function copySMSTemplate() {
            const area = document.getElementById("sms-text-area");
            area.select();
            document.execCommand("copy");
            alert("Rappel de relance copié dans le presse-papier !");
        }

        function highlightDebtRow(detteId) {
            // Reset previous highlights
            document.querySelectorAll("#debts-main-table tbody tr").forEach(tr => {
                tr.style.background = "";
                tr.style.borderLeft = "";
            });
            // Apply outline glow to selected row
            const targetRow = document.getElementById("debt-row-" + detteId);
            if (targetRow) {
                targetRow.style.background = "rgba(59, 130, 246, 0.08)";
                targetRow.style.borderLeft = "4px solid var(--accent)";
            }
        }

        // Approvisionnement Dynamic Cart Logic
        const supplyCart = [];

        function addSupplyItem(event) {
            event.preventDefault();
            const select = document.getElementById("supply-item-select");
            const name = select.options[select.selectedIndex].getAttribute("data-name");
            const id = select.value;
            const qty = parseInt(document.getElementById("supply-qty").value);
            const cost = parseFloat(document.getElementById("supply-cost").value);

            if (qty <= 0 || isNaN(cost) || cost < 0) {
                alert("Veuillez saisir une quantité et un coût d'achat valides !");
                return;
            }

            const existing = supplyCart.find(item => item.id === id);
            if (existing) {
                existing.qty += qty;
                existing.cost = cost;
                existing.total = existing.qty * cost;
            } else {
                supplyCart.push({ id, name, qty, cost, total: qty * cost });
            }

            renderSupplyCart();
            document.getElementById("supply-cost").value = "";
            hideKeypad();
        }

        function removeSupplyItem(index) {
            supplyCart.splice(index, 1);
            renderSupplyCart();
        }

        function renderSupplyCart() {
            const body = document.getElementById("supply-cart-rows");
            const textDisplay = document.getElementById("supply_total_display_text");
            const valueInput = document.getElementById("supply_total_display");
            const hiddenInputs = document.getElementById("hidden-supply-inputs");

            if (supplyCart.length === 0) {
                body.innerHTML = `
                    <tr id="empty-supply-row">
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 12px 0;">Aucun article ajouté.</td>
                    </tr>
                `;
                textDisplay.innerText = "0";
                valueInput.value = 0;
                hiddenInputs.innerHTML = "";
                return;
            }

            body.innerHTML = "";
            hiddenInputs.innerHTML = "";
            let overallTotal = 0;

            supplyCart.forEach((item, index) => {
                overallTotal += item.total;
                body.innerHTML += `
                    <tr>
                        <td style="padding: 6px 0; font-weight:700;">${item.name}</td>
                        <td style="padding: 6px 0;">${item.qty}</td>
                        <td style="padding: 6px 0;">${new Intl.NumberFormat('fr-FR').format(item.cost)} F</td>
                        <td style="padding: 6px 0; font-weight:800; color:var(--accent);">${new Intl.NumberFormat('fr-FR').format(item.total)} F</td>
                        <td style="padding: 6px 0; text-align:right;">
                            <button type="button" onclick="removeSupplyItem(${index})" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:12px;">🗑️</button>
                        </td>
                    </tr>
                `;
                hiddenInputs.innerHTML += `
                    <input type="hidden" name="product_ids[]" value="${item.id}">
                    <input type="hidden" name="product_qtys[]" value="${item.qty}">
                    <input type="hidden" name="product_costs[]" value="${item.cost}">
                `;
            });

            textDisplay.innerText = new Intl.NumberFormat('fr-FR').format(overallTotal);
            valueInput.value = overallTotal;
        }
        // Système de pagination côté client intelligent
        function initPaginatedTable(tableId, rowsPerPage = 10) {
            const table = document.getElementById(tableId);
            if (!table) return;

            const tbody = table.querySelector("tbody");
            if (!tbody) return;

            const allRows = Array.from(tbody.children);
            const groups = [];
            for (let i = 0; i < allRows.length; i++) {
                const r = allRows[i];
                const cells = r.querySelectorAll("td");
                if (cells.length > 0 && cells[0].getAttribute("colspan") === null) {
                    const nextRow = allRows[i + 1];
                    const hasDetail = nextRow && nextRow.querySelector(".details-drawer");
                    groups.push({
                        main: r,
                        detail: hasDetail ? nextRow : null
                    });
                }
            }

            let pagerContainer = document.getElementById(tableId + "-pager");
            if (!pagerContainer) {
                pagerContainer = document.createElement("div");
                pagerContainer.id = tableId + "-pager";
                pagerContainer.style.display = "flex";
                pagerContainer.style.justifyContent = "center";
                pagerContainer.style.alignItems = "center";
                pagerContainer.style.gap = "8px";
                pagerContainer.style.marginTop = "16px";
                pagerContainer.style.padding = "10px 0";
                table.parentNode.insertBefore(pagerContainer, table.nextSibling);
            }

            table.updatePagination = function() {
                const activeGroups = groups.filter(g => g.main.style.display !== "none");
                const totalPages = Math.ceil(activeGroups.length / rowsPerPage);
                let currentPage = 1;

                function showPage(page) {
                    if (page < 1) page = 1;
                    if (page > totalPages) page = totalPages;
                    currentPage = page;
                    
                    const start = (page - 1) * rowsPerPage;
                    const end = start + rowsPerPage;

                    activeGroups.forEach((group, idx) => {
                        const inRange = idx >= start && idx < end;
                        group.main.style.setProperty("display", inRange ? "" : "none", "important");
                        if (group.detail) {
                            if (!inRange) {
                                group.detail.style.display = "none";
                            } else {
                                const drawers = Array.from(group.detail.querySelectorAll(".details-drawer"));
                                const drawerVisible = drawers.some(dr => window.getComputedStyle(dr).display !== 'none');
                                group.detail.style.display = drawerVisible ? "" : "none";
                            }
                        }
                    });

                    groups.forEach(g => {
                        if (!activeGroups.includes(g)) {
                            g.main.style.display = "none";
                            if (g.detail) g.detail.style.display = "none";
                        }
                    });

                    renderPager();
                }

                function renderPager() {
                    pagerContainer.innerHTML = "";
                    if (totalPages <= 1) {
                        pagerContainer.style.display = "none";
                        return;
                    }
                    pagerContainer.style.display = "flex";

                    const prevBtn = document.createElement("button");
                    prevBtn.className = "btn-quick-action";
                    prevBtn.innerText = "◀";
                    prevBtn.disabled = currentPage === 1;
                    prevBtn.style.opacity = currentPage === 1 ? "0.4" : "1";
                    prevBtn.onclick = (e) => { e.preventDefault(); if (currentPage > 1) showPage(currentPage - 1); };
                    pagerContainer.appendChild(prevBtn);

                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const pageBtn = document.createElement("button");
                        pageBtn.className = "btn-quick-action";
                        pageBtn.innerText = i;
                        pageBtn.style.minWidth = "30px";
                        if (i === currentPage) {
                            pageBtn.style.background = "var(--accent)";
                            pageBtn.style.borderColor = "var(--accent)";
                            pageBtn.style.color = "white";
                        }
                        pageBtn.onclick = (e) => { e.preventDefault(); showPage(i); };
                        pagerContainer.appendChild(pageBtn);
                    }

                    const nextBtn = document.createElement("button");
                    nextBtn.className = "btn-quick-action";
                    nextBtn.innerText = "▶";
                    nextBtn.disabled = currentPage === totalPages;
                    nextBtn.style.opacity = currentPage === totalPages ? "0.4" : "1";
                    nextBtn.onclick = (e) => { e.preventDefault(); if (currentPage < totalPages) showPage(currentPage + 1); };
                    pagerContainer.appendChild(nextBtn);
                }

                showPage(1);
            };

            table.updatePagination();
        }
    </script>
</body>
</html>