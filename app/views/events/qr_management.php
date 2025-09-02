<?php
// Vue de gestion des QR codes pour les organisateurs
// À placer dans views/events/qr_management.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des QR Codes - <?= htmlspecialchars($event['title']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        
        .actions-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #5a6fd8;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .btn-secondary {
            background-color: #e9ecef;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #dee2e6;
        }
        
        .search-box {
            flex: 1;
            min-width: 200px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .participants-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            background-color: #667eea;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            font-size: 1.3em;
            font-weight: 600;
        }
        
        .table-controls {
            display: flex;
            gap: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-checked-in {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .qr-preview {
            width: 50px;
            height: 50px;
            cursor: pointer;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .qr-missing {
            width: 50px;
            height: 50px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            color: #666;
            font-size: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .qr-large {
            max-width: 300px;
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                min-width: auto;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Gestion des QR Codes</h1>
            <p><?= htmlspecialchars($event['title']) ?></p>
            <p><small><?= date('d/m/Y à H:i', strtotime($event['event_date'])) ?> - <?= htmlspecialchars($event['location']) ?></small></p>
        </div>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_participants'] ?></div>
                <div class="stat-label">Participants Inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['checked_in_count'] ?></div>
                <div class="stat-label">Déjà Enregistrés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['pending_checkin'] ?></div>
                <div class="stat-label">En Attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $qr_stats['with_qr'] ?? 0 ?></div>
                <div class="stat-label">QR Codes Générés</div>
            </div>
        </div>
        
        <!-- Barre d'actions -->
        <div class="actions-bar">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Rechercher un participant..." onkeyup="filterParticipants()">
            </div>
            <button class="btn btn-success" onclick="generateMissingQRs()">
                ⚡ Générer QR Manquants
            </button>
            <button class="btn btn-primary" onclick="showCheckinModal()">
                📱 Check-in Manuel
            </button>
            <button class="btn btn-warning" onclick="exportQRCodes()">
                📦 Exporter QR Codes
            </button>
            <a href="<?= BASE_URL ?>/events/show?id=<?= $event['id'] ?>" class="btn btn-secondary">
                ← Retour
            </a>
        </div>
        
        <!-- Tableau des participants -->
        <div class="participants-table">
            <div class="table-header">
                <div class="table-title">Liste des Participants</div>
                <div class="table-controls">
                    <select id="statusFilter" onchange="filterByStatus()">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="checked-in">Enregistrés</option>
                    </select>
                </div>
            </div>
            
            <table id="participantsTable">
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Email</th>
                        <th>QR Code</th>
                        <th>Statut</th>
                        <th>Code Check-in</th>
                        <th>Date Inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                    <tr data-status="<?= $participant['checked_in'] ? 'checked-in' : 'pending' ?>">
                        <td><?= htmlspecialchars($participant['participant']) ?></td>
                        <td><?= htmlspecialchars($participant['email']) ?></td>
                        <td>
                            <?php if (!empty($participant['qr_code_path']) && file_exists(__DIR__ . '/../../' . $participant['qr_code_path'])): ?>
                                <img src="<?= BASE_URL ?>/<?= $participant['qr_code_path'] ?>" 
                                     class="qr-preview" 
                                     onclick="showQRModal('<?= $participant['id'] ?>', '<?= BASE_URL ?>/<?= $participant['qr_code_path'] ?>')"
                                     alt="QR Code">
                            <?php else: ?>
                                <div class="qr-missing">N/A</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?= $participant['checked_in'] ? 'status-checked-in' : 'status-pending' ?>">
                                <?= $participant['checked_in'] ? 'Enregistré' : 'En attente' ?>
                            </span>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($participant['checkin_code']) ?></code>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($participant['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if (!$participant['checked_in']): ?>
                                    <button class="btn btn-success btn-sm" onclick="checkinParticipant('<?= $participant['checkin_code'] ?>')">
                                        ✅ Check-in
                                    </button>
                                <?php endif; ?>
                                
                                <?php if (empty($participant['qr_code_path'])): ?>
                                    <button class="btn btn-primary btn-sm" onclick="generateQR(<?= $participant['id'] ?>)">
                                        🔄 Générer QR
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-warning btn-sm" onclick="regenerateQR(<?= $participant['id'] ?>)">
                                        🔄 Régénérer
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal QR Code -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQRModal()">&times;</span>
            <h3>QR Code du Participant</h3>
            <img id="qrLargeImage" class="qr-large" src="" alt="QR Code">
            <div>
                <button class="btn btn-primary" onclick="downloadQRFromModal()">📱 Télécharger</button>
                <button class="btn btn-secondary" onclick="printQR()">🖨️ Imprimer</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Check-in Manuel -->
    <div id="checkinModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCheckinModal()">&times;</span>
            <h3>Check-in Manuel</h3>
            <p>Scannez le QR code ou saisissez le code manuellement :</p>
            <input type="text" id="checkinCodeInput" placeholder="Code de check-in" style="width: 100%; padding: 10px; margin: 20px 0; border: 1px solid #ddd; border-radius: 5px;">
            <div>
                <button class="btn btn-success" onclick="performCheckin()">✅ Effectuer Check-in</button>
                <button class="btn btn-secondary" onclick="closeCheckinModal()">Annuler</button>
            </div>
        </div>
    </div>
    
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        const EVENT_ID = <?= $event['id'] ?>;
        
        function filterParticipants() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('participantsTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
        
        function filterByStatus() {
            const select = document.getElementById('statusFilter');
            const filter = select.value;
            const table = document.getElementById('participantsTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const status = rows[i].getAttribute('data-status');
                
                if (filter === '' || status === filter) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
        
        function showQRModal(participantId, qrPath) {
            const modal = document.getElementById('qrModal');
            const image = document.getElementById('qrLargeImage');
            image.src = qrPath;
            image.setAttribute('data-participant-id', participantId);
            modal.style.display = 'block';
        }
        
        function closeQRModal() {
            document.getElementById('qrModal').style.display = 'none';
        }
        
        function showCheckinModal() {
            document.getElementById('checkinModal').style.display = 'block';
            document.getElementById('checkinCodeInput').focus();
        }
        
        function closeCheckinModal() {
            document.getElementById('checkinModal').style.display = 'none';
            document.getElementById('checkinCodeInput').value = '';
        }
        
        function downloadQRFromModal() {
            const image = document.getElementById('qrLargeImage');
            const link = document.createElement('a');
            link.download = 'qr_participant_' + image.getAttribute('data-participant-id') + '.png';
            link.href = image.src;
            link.click();
        }
        
        function printQR() {
            const image = document.getElementById('qrLargeImage');
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head><title>QR Code</title></head>
                    <body style="text-align: center; padding: 20px;">
                        <img src="${image.src}" style="max-width: 400px;">
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
        
        function checkinParticipant(checkinCode) {
            if (confirm('Confirmer le check-in de ce participant ?')) {
                fetch(`${BASE_URL}/participation/checkin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `code=${checkinCode}&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Check-in effectué avec succès !');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du check-in');
                });
            }
        }
        
        function performCheckin() {
            const code = document.getElementById('checkinCodeInput').value.trim();
            if (code) {
                checkinParticipant(code);
                closeCheckinModal();
            } else {
                alert('Veuillez saisir un code de check-in');
            }
        }
        
        function generateQR(participationId) {
            fetch(`${BASE_URL}/participation/generateQR`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `participation_id=${participationId}&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('QR Code généré avec succès !');
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la génération du QR Code');
            });
        }
        
        function regenerateQR(participationId) {
            if (confirm('Régénérer le QR Code de ce participant ?')) {
                generateQR(participationId);
            }
        }
        
        function generateMissingQRs() {
            if (confirm('Générer tous les QR Codes manquants pour cet événement ?')) {
                fetch(`${BASE_URL}/events/generateMissingQRs`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `event_id=${EVENT_ID}&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${data.generated} QR Codes générés avec succès !`);
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la génération des QR Codes');
                });
            }
        }
        
        function exportQRCodes() {
            window.location.href = `${BASE_URL}/events/exportQRCodes?event_id=${EVENT_ID}`;
        }
        
        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            const qrModal = document.getElementById('qrModal');
            const checkinModal = document.getElementById('checkinModal');
            
            if (event.target === qrModal) {
                closeQRModal();
            }
            if (event.target === checkinModal) {
                closeCheckinModal();
            }
        }
        
        // Raccourci clavier pour le check-in manuel
        document.addEventListener('keydown', function(event) {
            if (event.ctrlKey && event.key === 'k') {
                event.preventDefault();
                showCheckinModal();
            }
        });
    </script>
</body>
</html>

