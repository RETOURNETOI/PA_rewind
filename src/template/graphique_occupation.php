<?php
// graphique_occupation.php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: connexion.php");
    exit;
}

// Connexion BDD
$dsn = "mysql:host=localhost;dbname=kayak_trip;charset=utf8";
try {
    $db = new PDO($dsn, "root", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

// Récupération des données d'occupation
$periode = $_GET['periode'] ?? 'mois'; // mois, trimestre, annee

// Calcul du taux d'occupation par hébergement
$sql = "SELECT 
    h.id_hebergement,
    h.nom,
    h.type,
    h.capacite,
    pa.nom as point_nom,
    COUNT(ch.id_commande) as total_reservations,
    COALESCE(SUM(DATEDIFF(ch.date_fin, ch.date_debut)), 0) as nuits_reservees
FROM hebergement h
LEFT JOIN point_arret pa ON h.id_point = pa.id_point
LEFT JOIN commande_hebergement ch ON h.id_hebergement = ch.id_hebergement
    AND ch.date_debut >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY h.id_hebergement
ORDER BY nuits_reservees DESC";

$hebergements = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Données pour les graphiques
$labels = [];
$taux_occupation = [];
$reservations_data = [];

foreach ($hebergements as $heb) {
    $labels[] = $heb['nom'];
    // Calcul approximatif du taux d'occupation (sur 6 mois = 180 jours)
    $taux = $heb['capacite'] > 0 ? min(100, ($heb['nuits_reservees'] / (180 * $heb['capacite'])) * 100) : 0;
    $taux_occupation[] = round($taux, 1);
    $reservations_data[] = $heb['total_reservations'];
}

// Statistiques globales
$total_hebergements = count($hebergements);
$taux_moyen = $total_hebergements > 0 ? array_sum($taux_occupation) / $total_hebergements : 0;
$total_reservations = array_sum($reservations_data);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Graphique Taux d'Occupation</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-bar { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .chart-section { background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .btn { padding: 10px 20px; background: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-active { background: #28a745; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .taux-excellent { color: #28a745; font-weight: bold; }
        .taux-bon { color: #17a2b8; }
        .taux-moyen { color: #ffc107; }
        .taux-faible { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Graphique Taux d'Occupation des Hébergements</h1>
            <p>Analyse des performances et statistiques de réservation</p>
            <a href="dashboard_admin.php" class="btn">← Dashboard</a>
            <a href="gestion_hebergements.php" class="btn">Gérer Hébergements</a>
        </div>

        <!-- Filtres -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>Période d'analyse</h3>
            <a href="?periode=mois" class="btn <?= $periode == 'mois' ? 'btn-active' : '' ?>">Dernier mois</a>
            <a href="?periode=trimestre" class="btn <?= $periode == 'trimestre' ? 'btn-active' : '' ?>">Dernier trimestre</a>
            <a href="?periode=annee" class="btn <?= $periode == 'annee' ? 'btn-active' : '' ?>">Dernière année</a>
        </div>

        <!-- Statistiques -->
        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= $total_hebergements ?></h3>
                <p>Hébergements analysés</p>
            </div>
            <div class="stat-card">
                <h3><?= round($taux_moyen, 1) ?>%</h3>
                <p>Taux d'occupation moyen</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_reservations ?></h3>
                <p>Total réservations</p>
            </div>
            <div class="stat-card">
                <h3><?= count(array_filter($taux_occupation, fn($t) => $t > 70)) ?></h3>
                <p>Hébergements > 70%</p>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-grid">
            <div class="chart-section">
                <h3>Taux d'Occupation par Hébergement</h3>
                <canvas id="occupationChart" width="400" height="300"></canvas>
            </div>
            <div class="chart-section">
                <h3>Nombre de Réservations</h3>
                <canvas id="reservationsChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Graphique de tendance -->
        <div class="chart-section">
            <h3>Répartition des Performances</h3>
            <canvas id="performanceChart" width="800" height="400"></canvas>
        </div>

        <!-- Tableau détaillé -->
        <div class="chart-section">
            <h3>Détail par Hébergement</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Point d'Arrêt</th>
                            <th>Capacité</th>
                            <th>Réservations</th>
                            <th>Nuits Réservées</th>
                            <th>Taux d'Occupation</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hebergements as $i => $heb): 
                            $taux = $taux_occupation[$i];
                            $classe = $taux >= 80 ? 'taux-excellent' : 
                                     ($taux >= 60 ? 'taux-bon' : 
                                     ($taux >= 40 ? 'taux-moyen' : 'taux-faible'));
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($heb['nom']) ?></strong></td>
                            <td><?= ucfirst($heb['type']) ?></td>
                            <td><?= htmlspecialchars($heb['point_nom']) ?></td>
                            <td><?= $heb['capacite'] ?> pers.</td>
                            <td><?= $heb['total_reservations'] ?></td>
                            <td><?= $heb['nuits_reservees'] ?></td>
                            <td class="<?= $classe ?>"><?= $taux ?>%</td>
                            <td>
                                <?php if ($taux >= 80): ?>
                                    <span style="color: #28a745;">🔥 Excellent</span>
                                <?php elseif ($taux >= 60): ?>
                                    <span style="color: #17a2b8;">👍 Bon</span>
                                <?php elseif ($taux >= 40): ?>
                                    <span style="color: #ffc107;">⚠️ Moyen</span>
                                <?php else: ?>
                                    <span style="color: #dc3545;">📉 Faible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recommandations -->
        <div class="chart-section">
            <h3>🎯 Recommandations</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>Hébergements performants (>70%)</h4>
                    <ul>
                        <?php 
                        $performants = array_filter($hebergements, fn($h, $i) => $taux_occupation[$i] > 70, ARRAY_FILTER_USE_BOTH);
                        foreach (array_slice($performants, 0, 5) as $i => $heb): 
                        ?>
                            <li><?= htmlspecialchars($heb['nom']) ?> (<?= $taux_occupation[array_search($heb, $hebergements)] ?>%)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4>À améliorer (<40%)</h4>
                    <ul>
                        <?php 
                        $faibles = array_filter($hebergements, fn($h, $i) => $taux_occupation[$i] < 40, ARRAY_FILTER_USE_BOTH);
                        foreach (array_slice($faibles, 0, 5) as $i => $heb): 
                        ?>
                            <li><?= htmlspecialchars($heb['nom']) ?> (<?= $taux_occupation[array_search($heb, $hebergements)] ?>%)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Données PHP vers JavaScript
        const labels = <?= json_encode(array_slice($labels, 0, 10)) ?>;
        const tauxOccupation = <?= json_encode(array_slice($taux_occupation, 0, 10)) ?>;
        const reservationsData = <?= json_encode(array_slice($reservations_data, 0, 10)) ?>;

        // Graphique taux d'occupation
        const ctx1 = document.getElementById('occupationChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Taux d\'occupation (%)',
                    data: tauxOccupation,
                    backgroundColor: tauxOccupation.map(t => 
                        t >= 80 ? '#28a745' : 
                        t >= 60 ? '#17a2b8' : 
                        t >= 40 ? '#ffc107' : '#dc3545'
                    ),
                    borderWidth: 1
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '% d\'occupation';
                            }
                        }
                    }
                }
            }
        });

        // Graphique réservations
        const ctx2 = document.getElementById('reservationsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: reservationsData,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique de performance globale
        const ctx3 = document.getElementById('performanceChart').getContext('2d');
        const performanceCategories = ['Excellent (>80%)', 'Bon (60-80%)', 'Moyen (40-60%)', 'Faible (<40%)'];
        const performanceCounts = [
            tauxOccupation.filter(t => t >= 80).length,
            tauxOccupation.filter(t => t >= 60 && t < 80).length,
            tauxOccupation.filter(t => t >= 40 && t < 60).length,
            tauxOccupation.filter(t => t < 40).length
        ];

        new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: performanceCategories,
                datasets: [{
                    data: performanceCounts,
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    </script>
</body>
</html>