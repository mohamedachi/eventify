<?php
class ParticipationController extends Controller {
    public function toggle() {
        Auth::requireRole(['participant','organizer','admin']);
        Auth::verifyCsrf();
        $eventId = (int)($_POST['event_id'] ?? 0);
        $userId = Auth::id();
        $res = (new Participation())->toggle($eventId,$userId);
        $this->redirect('events/show?id='.$eventId);
    }
    public function exportCsv() {
        Auth::requireRole(['admin','organizer']);
        $eventId = (int)($_GET['event_id'] ?? 0);
        $parts = (new Participation())->forEvent($eventId);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="participants_event_'.$eventId.'.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id','participant','email','checked_in','checkin_code']);
        foreach($parts as $p) {
            fputcsv($out, [$p['id'],$p['participant'],$p['email'] ?? '',$p['checked_in'],$p['checkin_code']]);
        }
        fclose($out);
        exit;
    }
    public function checkin() {
        Auth::requireRole(['admin','organizer']);
        Auth::verifyCsrf();
        $code = $this->input('code');
        $stmt = Database::getConnection()->prepare("SELECT * FROM participations WHERE checkin_code=? LIMIT 1");
        $stmt->execute([$code]);
        $p = $stmt->fetch();
        if (!$p) { echo 'Invalid code'; return; }
        Database::getConnection()->prepare("UPDATE participations SET checked_in=1 WHERE id=?")->execute([$p['id']]);
        $this->redirect('events/show?id='.$p['event_id']);
    }
}