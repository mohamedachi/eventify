<?php
class Participation extends Model {
    public function toggle(int $eventId, int $userId) {
        $stmt = $this->db->prepare("SELECT * FROM participations WHERE event_id=? AND user_id=?");
        $stmt->execute([$eventId,$userId]);
        $row = $stmt->fetch();
        if ($row) {
            $del = $this->db->prepare("DELETE FROM participations WHERE id=?");
            $del->execute([$row['id']]);
            // promote from waitlist
            $prom = $this->db->prepare("SELECT * FROM waitlist WHERE event_id=? ORDER BY id ASC LIMIT 1");
            $prom->execute([$eventId]);
            $p = $prom->fetch();
            if ($p) {
                $this->db->prepare("INSERT INTO participations(event_id,user_id,checkin_code) VALUES (?,?,?)")->execute([$eventId,$p['user_id'],bin2hex(random_bytes(8))]);
                $this->db->prepare("DELETE FROM waitlist WHERE id=?")->execute([$p['id']]);
                return ['status'=>'left','promoted'=>$p['user_id']];
            }
            return ['status'=>'left'];
        }
        // check capacity
        $ev = $this->db->prepare("SELECT capacity FROM events WHERE id=?"); $ev->execute([$eventId]); $e = $ev->fetch();
        $count = $this->db->prepare("SELECT COUNT(*) as c FROM participations WHERE event_id=?"); $count->execute([$eventId]); $c = (int)$count->fetchColumn();
        if ($e && $e['capacity']>0 && $c >= $e['capacity']) {
            // add to waitlist
            $ins = $this->db->prepare("INSERT IGNORE INTO waitlist(event_id,user_id) VALUES (?,?)");
            $ins->execute([$eventId,$userId]);
            return ['status'=>'waitlisted'];
        } else {
            // join normally, generate checkin code
            $code = bin2hex(random_bytes(8));
            $ins = $this->db->prepare("INSERT INTO participations(event_id, user_id, checkin_code) VALUES (?,?,?)");
            $ins->execute([$eventId,$userId,$code]);
            return ['status'=>'joined','code'=>$code];
        }
    }
    public function forEvent(int $eventId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as participant, u.email as email
            FROM participations p
            JOIN users u ON p.user_id=u.id
            WHERE p.event_id=?
            ORDER BY p.id DESC
        ");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }
    public function byUser(int $userId) {
        $stmt = $this->db->prepare("
            SELECT e.* FROM participations p
            JOIN events e ON p.event_id=e.id
            WHERE p.user_id=? ORDER BY e.event_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}