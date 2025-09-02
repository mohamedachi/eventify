<?php
class EventController extends Controller {
    public function index() {
        $events = (new Event())->all();
        $this->view('events/index', ['events'=>$events]);
    }
    
    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        $event = (new Event())->find($id);
        if (!$event) { http_response_code(404); echo 'Event not found'; return; }
        $participants = (new Participation())->forEvent($id);
        $this->view('events/show', ['event'=>$event,'participants'=>$participants]);
    }
    
    public function create() {
        Auth::requireRole(['admin','organizer']);
        $this->view('events/create', [], 'back');
    }
    
    public function store() {
        Auth::requireRole(['admin','organizer']);
        Auth::verifyCsrf();
        $data = [
            'title'=>$this->input('title'),
            'description'=>$this->input('description'),
            'location'=>$this->input('location'),
            'event_date'=>$this->input('event_date'),
            'price'=>(float)($_POST['price'] ?? 0),
            'capacity'=>(int)($_POST['capacity'] ?? 0),
            'status'=>$this->input('status') ?: 'draft', // Changé: par défaut en draft
            'user_id'=>Auth::id(),
        ];
        // handle upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $updir = __DIR__ . '/../../public/uploads'; if (!is_dir($updir)) mkdir($updir, 0755, true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $name = uniqid('img_').'.'.($ext?:'jpg');
            move_uploaded_file($_FILES['image']['tmp_name'], $updir.'/'.$name);
            $data['image'] = 'public/uploads/'.$name;
        }
        $id = (new Event())->create($data);
        $this->redirect('events/show?id='.$id);
    }
    
    public function edit() {
        Auth::requireRole(['admin','organizer']);
        $id = (int)($_GET['id'] ?? 0);
        $event = (new Event())->find($id);
        $this->view('events/edit', ['event'=>$event], 'back');
    }
    
    public function update() {
        Auth::requireRole(['admin','organizer']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'title'=>$this->input('title'),
            'description'=>$this->input('description'),
            'location'=>$this->input('location'),
            'event_date'=>$this->input('event_date'),
            'price'=>(float)($_POST['price'] ?? 0),
            'capacity'=>(int)($_POST['capacity'] ?? 0),
            'status'=>$this->input('status') ?: 'draft',
        ];
        if (!empty($_FILES['image']['tmp_name'])) {
            $updir = __DIR__ . '/../../public/uploads'; if (!is_dir($updir)) mkdir($updir, 0755, true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $name = uniqid('img_').'.'.($ext?:'jpg');
            move_uploaded_file($_FILES['image']['tmp_name'], $updir.'/'.$name);
            Database::getConnection()->prepare("UPDATE events SET image=? WHERE id=?")->execute(['public/uploads/'.$name,$id]);
        }
        (new Event())->updateEvent($id,$data);
        $this->redirect('events/show?id='.$id);
    }
    
    public function delete() {
        Auth::requireRole(['admin','organizer']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        (new Event())->deleteEvent($id);
        $this->redirect('events');
    }
    
    // Nouvelles méthodes pour le workflow de statut
    
    public function submitForApproval() {
        Auth::requireRole(['organizer']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        
        // Vérifier que l'événement appartient à l'organisateur
        $event = (new Event())->find($id);
        if (!$event || $event['user_id'] != Auth::id()) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        
        if ($event['status'] !== 'draft') {
            echo 'Seuls les événements en brouillon peuvent être soumis pour approbation';
            return;
        }
        
        Database::getConnection()->prepare("UPDATE events SET status='pending' WHERE id=?")->execute([$id]);
        $this->redirect('events/show?id='.$id);
    }
    
    public function approve() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        
        $event = (new Event())->find($id);
        if (!$event || $event['status'] !== 'pending') {
            echo 'Seuls les événements en attente peuvent être approuvés';
            return;
        }
        
        Database::getConnection()->prepare("UPDATE events SET status='published' WHERE id=?")->execute([$id]);
        $this->redirect('dashboard');
    }
    
    public function reject() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $reason = $this->input('reason') ?? '';
        
        $event = (new Event())->find($id);
        if (!$event || $event['status'] !== 'pending') {
            echo 'Seuls les événements en attente peuvent être rejetés';
            return;
        }
        
        Database::getConnection()->prepare("UPDATE events SET status='draft' WHERE id=?")->execute([$id]);
        
        // Optionnel: envoyer une notification à l'organisateur avec la raison du rejet
        // Cette fonctionnalité nécessiterait une table de notifications
        
        $this->redirect('dashboard');
    }
    
    public function archive() {
        Auth::requireRole(['admin', 'organizer']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        
        $event = (new Event())->find($id);
        if (!$event) {
            http_response_code(404);
            echo 'Événement non trouvé';
            return;
        }
        
        // Vérifier les permissions
        if (Auth::role() === 'organizer' && $event['user_id'] != Auth::id()) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        
        Database::getConnection()->prepare("UPDATE events SET status='archived' WHERE id=?")->execute([$id]);
        $this->redirect('events/show?id='.$id);
    }
    
    public function pendingEvents() {
        Auth::requireRole(['admin']);
        
        $stmt = Database::getConnection()->query("
            SELECT e.*, u.name as organizer 
            FROM events e 
            JOIN users u ON e.user_id = u.id 
            WHERE e.status = 'pending' 
            ORDER BY e.created_at ASC
        ");
        $pendingEvents = $stmt->fetchAll();
        
        $this->view('events/pending', ['events' => $pendingEvents], 'back');
    }
    
    public function statusHistory() {
        Auth::requireRole(['admin', 'organizer']);
        $id = (int)($_GET['id'] ?? 0);
        
        $event = (new Event())->find($id);
        if (!$event) {
            http_response_code(404);
            echo 'Événement non trouvé';
            return;
        }
        
        // Vérifier les permissions pour les organisateurs
        if (Auth::role() === 'organizer' && $event['user_id'] != Auth::id()) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        
        // Cette fonctionnalité nécessiterait une table d'historique des statuts
        // Pour l'instant, on affiche juste le statut actuel
        $this->view('events/status_history', ['event' => $event], 'back');
    }
    
    // Méthodes pour les statistiques rapides
    
    public function quickStats() {
        Auth::requireRole(['admin', 'organizer']);
        $id = (int)($_GET['id'] ?? 0);
        
        $event = (new Event())->find($id);
        if (!$event) {
            http_response_code(404);
            echo 'Événement non trouvé';
            return;
        }
        
        // Vérifier les permissions pour les organisateurs
        if (Auth::role() === 'organizer' && $event['user_id'] != Auth::id()) {
            http_response_code(403);
            echo 'Accès refusé';
            return;
        }
        
        $db = Database::getConnection();
        
        // Statistiques de participation
        $participationStats = $db->prepare("
            SELECT 
                COUNT(*) as total_participants,
                COUNT(CASE WHEN checked_in = 1 THEN 1 END) as checked_in_count,
                (COUNT(*) * ?) as total_revenue
            FROM participations 
            WHERE event_id = ?
        ");
        $participationStats->execute([$event['price'], $id]);
        $stats = $participationStats->fetch();
        
        // Inscriptions par jour (derniers 30 jours)
        $dailySignups = $db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as signups
            FROM participations 
            WHERE event_id = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $dailySignups->execute([$id]);
        $signupData = $dailySignups->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode([
            'event' => $event,
            'stats' => $stats,
            'daily_signups' => $signupData
        ]);
        exit;
    }
}

