<?php
// Vue PHP pour le tableau de bord des statistiques
// À placer dans views/statistics/dashboard.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord des Statistiques - <?= htmlspecialchars($site_name ?? 'Event Platform') ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/statistics.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Tableau de Bord des Statistiques</h1>
            <p>Analyse des participations et revenus de vos événements</p>
            <?php if (Auth::role() === 'organizer'): ?>
                <p><em>Vos événements uniquement</em></p>
            <?php endif; ?>
        </div>
        
        <!-- Statistiques générales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $generalStats['total_events'] ?? 0 ?></div>
                <div class="stat-label">Événements Totaux</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $generalStats['total_participations'] ?? 0 ?></div>
                <div class="stat-label">Participations Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $generalStats['total_users'] ?? 0 ?></div>
                <div class="stat-label">Utilisateurs Inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($generalStats['total_revenue'] ?? 0, 2) ?>€</div>
                <div class="stat-label">Revenus Totaux</div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="charts-container">
            <!-- Graphique des participations -->
            <div class="chart-card">
                <h3 class="chart-title">Évolution des Participations</h3>
                <div class="controls">
                    <button class="btn btn-primary active" onclick="updateParticipationChart('daily')" data-period="daily">Quotidien</button>
                    <button class="btn btn-secondary" onclick="updateParticipationChart('weekly')" data-period="weekly">Hebdomadaire</button>
                    <button class="btn btn-secondary" onclick="updateParticipationChart('monthly')" data-period="monthly">Mensuel</button>
                </div>
                <div class="chart-container">
                    <canvas id="participationChart"></canvas>
                </div>
            </div>
            
            <!-- Graphique des revenus -->
            <div class="chart-card">
                <h3 class="chart-title">Évolution des Revenus</h3>
                <div class="controls">
                    <button class="btn btn-primary active" onclick="updateRevenueChart('daily')" data-period="daily">Quotidien</button>
                    <button class="btn btn-secondary" onclick="updateRevenueChart('weekly')" data-period="weekly">Hebdomadaire</button>
                    <button class="btn btn-secondary" onclick="updateRevenueChart('monthly')" data-period="monthly">Mensuel</button>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tableau des revenus par événement -->
        <div class="revenue-table">
            <div class="table-title">Top 10 des Événements par Revenus</div>
            <table>
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Prix du Ticket</th>
                        <th>Participants</th>
                        <th>Revenus Totaux</th>
                        <th>Date de l'Événement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($revenueStats)): ?>
                        <?php foreach ($revenueStats as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= number_format($event['price'], 2) ?>€</td>
                                <td><?= $event['participants'] ?></td>
                                <td><strong><?= number_format($event['total_revenue'], 2) ?>€</strong></td>
                                <td><?= date('d/m/Y', strtotime($event['event_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">Aucune donnée disponible</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Boutons d'export -->
        <div class="export-buttons">
            <a href="<?= BASE_URL ?>/statistics/exportParticipationReport?format=csv" class="btn btn-primary">
                📊 Exporter Participations (CSV)
            </a>
            <a href="<?= BASE_URL ?>/statistics/exportRevenueReport?format=csv" class="btn btn-primary">
                💰 Exporter Revenus (CSV)
            </a>
        </div>
        
        <!-- Navigation de retour -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">← Retour au tableau de bord</a>
        </div>
    </div>
    
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        let participationChart = null;
        let revenueChart = null;
        
        // Configuration des graphiques
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Nombre'
                    }
                }
            }
        };
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });
        
        function initializeCharts() {
            // Graphique des participations
            const participationCtx = document.getElementById('participationChart').getContext('2d');
            participationChart = new Chart(participationCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Participations',
                        data: [],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: chartOptions
            });
            
            // Graphique des revenus
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Revenus (€)',
                        data: [],
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        ...chartOptions.scales,
                        y: {
                            ...chartOptions.scales.y,
                            title: {
                                display: true,
                                text: 'Revenus (€)'
                            }
                        }
                    }
                }
            });
            
            // Charger les données initiales
            updateParticipationChart('daily');
            updateRevenueChart('daily');
        }
        
        function updateParticipationChart(period) {
            // Mettre à jour les boutons actifs
            updateActiveButton(event.target, 'participation');
            
            // Appel AJAX pour récupérer les données
            fetch(`${BASE_URL}/statistics/participationChart?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => {
                        const date = new Date(item.date_label);
                        if (period === 'daily') {
                            return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
                        } else if (period === 'weekly') {
                            return `Sem. ${getWeekNumber(date)}`;
                        } else {
                            return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                        }
                    });
                    const participations = data.map(item => parseInt(item.participations));
                    
                    participationChart.data.labels = labels;
                    participationChart.data.datasets[0].data = participations;
                    participationChart.update();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données de participation:', error);
                });
        }
        
        function updateRevenueChart(period) {
            // Mettre à jour les boutons actifs
            updateActiveButton(event.target, 'revenue');
            
            // Appel AJAX pour récupérer les données
            fetch(`${BASE_URL}/statistics/revenueChart?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    const labels = data.map(item => {
                        const date = new Date(item.date_label);
                        if (period === 'daily') {
                            return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
                        } else if (period === 'weekly') {
                            return `Sem. ${getWeekNumber(date)}`;
                        } else {
                            return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                        }
                    });
                    const revenues = data.map(item => parseFloat(item.revenue));
                    
                    revenueChart.data.labels = labels;
                    revenueChart.data.datasets[0].data = revenues;
                    revenueChart.update();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données de revenus:', error);
                });
        }
        
        function updateActiveButton(clickedButton, chartType) {
            // Trouver le conteneur parent des boutons
            const buttonsContainer = clickedButton.parentElement;
            
            // Retirer la classe active de tous les boutons
            buttonsContainer.querySelectorAll('.btn').forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-secondary');
            });
            
            // Ajouter la classe active au bouton cliqué
            clickedButton.classList.add('active', 'btn-primary');
            clickedButton.classList.remove('btn-secondary');
        }
        
        function getWeekNumber(date) {
            const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
            const dayNum = d.getUTCDay() || 7;
            d.setUTCDate(d.getUTCDate() + 4 - dayNum);
            const yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
            return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
        }
    </script>
</body>
</html>

