<?php
class User extends Model {
    public function create(string $name, string $email, string $password, string $role): int {
        $stmt = $this->db->prepare("INSERT INTO users(name,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        return (int)$this->db->lastInsertId();
    }
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public function find(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function all() {
        return $this->db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
    }
    public function updateUser(int $id, string $name, string $email, ?string $password, string $role) {
        if ($password) {
            $stmt = $this->db->prepare("UPDATE users SET name=?, email=?, password=?, role=? WHERE id=?");
            return $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
            return $stmt->execute([$name, $email, $role, $id]);
        }
    }
    public function deleteUser(int $id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}