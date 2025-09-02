<?php
class Event extends Model {
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO events(title, description, location, event_date, price, user_id, capacity, status, image) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$data['title'],$data['description'],$data['location'],$data['event_date'],$data['price'],$data['user_id'],$data['capacity'],$data['status'],$data['image'] ?? null]);
        return (int)$this->db->lastInsertId();
    }
    public function updateEvent(int $id, array $data) {
        $stmt = $this->db->prepare("UPDATE events SET title=?, description=?, location=?, event_date=?, price=?, capacity=?, status=? WHERE id=?");
        return $stmt->execute([$data['title'],$data['description'],$data['location'],$data['event_date'],$data['price'],$data['capacity'],$data['status'],$id]);
    }
    public function deleteEvent(int $id) {
        $this->db->prepare("DELETE FROM participations WHERE event_id=?")->execute([$id]);
        $stmt = $this->db->prepare("DELETE FROM events WHERE id=?");
        return $stmt->execute([$id]);
    }
    public function find(int $id) {
        $stmt = $this->db->prepare("SELECT e.*, u.name as organizer FROM events e JOIN users u ON e.user_id=u.id WHERE e.id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function all() {
        return $this->db->query("SELECT e.*, u.name as organizer FROM events e JOIN users u ON e.user_id=u.id ORDER BY event_date DESC")->fetchAll();
    }
    public function byUser(int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE user_id=? ORDER BY event_date DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function stats() {
        return $this->db->query("
            SELECT 
                COUNT(*) as total_events,
                COALESCE(SUM(price),0) as total_potential_revenue,
                (SELECT COUNT(*) FROM participations) as total_participations
            FROM events
        ")->fetch();
    }
}