<?php
// Vue pour afficher le QR code d'un participant
// À placer dans views/participation/qr.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Ticket QR - <?= htmlspecialchars($event['title']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .ticket-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            position: relative;
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #764ba2;
        }
        
        .ticket-title {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .ticket-subtitle {
            opacity: 0.9;
            font-size: 1em;
        }
        
        .ticket-body {
            padding: 30px 20px;
            text-align: center;
        }
        
        .event-info {
            margin-bottom: 30px;
        }
        
        .event-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        
        .event-details {
            color: #666;
            line-height: 1.6;
        }
        
        .event-detail {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        
        .event-detail-icon {
            margin-right: 8px;
            font-size: 1.1em;
        }
        
        .qr-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            border: 2px dashed #dee2e6;
        }
        
        .qr-title {
            font-size: 1.1em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        
        .qr-code {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .qr-code img {
            max-width: 200px;
            height: auto;
            display: block;
        }
        
        .qr-fallback {
            background: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            color: #666;
        }
        
        .checkin-code {
            margin-top: 15px;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #1976d2;
            font-size: 1.1em;
        }
        
        .instructions {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 10px;
            color: #856404;
            font-size: 0.9em;
            line-height: 1.5;
        }
        
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
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
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #e9ecef;
            color: #333;
        }
        
        .btn-secondary:hover {
            background-color: #dee2e6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-checked-in {
            background-color: #d4edda;
            color: #155724;
        }
        
        @media (max-width: 480px) {
            .ticket-container {
                margin: 10px;
            }
            
            .ticket-header {
                padding: 20px 15px;
            }
            
            .ticket-body {
                padding: 20px 15px;
            }
            
            .qr-code img {
                max-width: 150px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .ticket-container {
                box-shadow: none;
                border: 2px solid #333;
            }
            
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <div class="ticket-title">🎫 Mon Ticket</div>
            <div class="ticket-subtitle">Événement confirmé</div>
        </div>
        
        <div class="ticket-body">
            <div class="event-info">
                <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                <div class="event-details">
                    <div class="event-detail">
                        <span class="event-detail-icon">📅</span>
                        <span><?= date('d/m/Y à H:i', strtotime($event['event_date'])) ?></span>
                    </div>
                    <div class="event-detail">
                        <span class="event-detail-icon">📍</span>
                        <span><?= htmlspecialchars($event['location']) ?></span>
                    </div>
                    <?php if ($event['price'] > 0): ?>
                    <div class="event-detail">
                        <span class="event-detail-icon">💰</span>
                        <span><?= number_format($event['price'], 2) ?>€</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="status-badge <?= $participation['checked_in'] ? 'status-checked-in' : 'status-pending' ?>">
                    <?= $participation['checked_in'] ? '✅ Enregistré' : '⏳ En attente' ?>
                </div>
            </div>
            
            <div class="qr-section">
                <div class="qr-title">🔍 Code QR d'entrée</div>
                
                <?php if (!empty($participation['qr_code_path']) && file_exists(__DIR__ . '/../../' . $participation['qr_code_path'])): ?>
                    <div class="qr-code">
                        <img src="<?= BASE_URL ?>/<?= $participation['qr_code_path'] ?>" 
                             alt="QR Code de participation" 
                             id="qrImage">
                    </div>
                <?php else: ?>
                    <div class="qr-fallback">
                        <p>QR Code non disponible</p>
                        <p><small>Utilisez le code ci-dessous</small></p>
                    </div>
                <?php endif; ?>
                
                <div class="checkin-code">
                    Code: <?= htmlspecialchars($participation['checkin_code']) ?>
                </div>
            </div>
            
            <div class="instructions">
                <strong>Instructions:</strong><br>
                • Présentez ce QR code à l'entrée de l'événement<br>
                • Gardez ce ticket accessible sur votre téléphone<br>
                • En cas de problème, montrez le code alphanumérique
            </div>
            
            <div class="actions">
                <button class="btn btn-primary" onclick="downloadQR()">📱 Télécharger QR</button>
                <button class="btn btn-secondary" onclick="window.print()">🖨️ Imprimer</button>
                <a href="<?= BASE_URL ?>/events/show?id=<?= $event['id'] ?>" class="btn btn-secondary">← Retour à l'événement</a>
            </div>
        </div>
    </div>
    
    <script>
        function downloadQR() {
            const qrImage = document.getElementById('qrImage');
            if (qrImage) {
                const link = document.createElement('a');
                link.download = 'ticket_qr_<?= $event['id'] ?>_<?= $participation['id'] ?>.png';
                link.href = qrImage.src;
                link.click();
            } else {
                alert('QR Code non disponible pour le téléchargement');
            }
        }
        
        // Auto-refresh du statut toutes les 30 secondes si pas encore enregistré
        <?php if (!$participation['checked_in']): ?>
        setInterval(function() {
            fetch('<?= BASE_URL ?>/participation/checkStatus?id=<?= $participation['id'] ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.checked_in) {
                        location.reload();
                    }
                })
                .catch(error => console.log('Erreur vérification statut:', error));
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>

