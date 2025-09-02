<?php
class UserController extends Controller {
    public function index() {
        Auth::requireRole(['admin']);
        $users = (new User())->all();
        $this->view('users/index', ['users'=>$users], 'back');
    }
    public function create() {
        Auth::requireRole(['admin']);
        $this->view('users/create', [], 'back');
    }
    public function store() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $name = $this->input('name'); $email = $this->input('email');
        $password = $_POST['password'] ?? '';
        $role = in_array($_POST['role'] ?? 'participant', ['admin','organizer','participant']) ? $_POST['role'] : 'participant';
        (new User())->create($name,$email,$password,$role);
        $this->redirect('users');
    }
    public function edit() {
        Auth::requireRole(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        $user = (new User())->find($id);
        $this->view('users/edit', ['user'=>$user], 'back');
    }
    public function update() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = $this->input('name'); $email = $this->input('email');
        $password = $_POST['password'] ?? null;
        if ($password === '') $password = null;
        $role = in_array($_POST['role'] ?? 'participant', ['admin','organizer','participant']) ? $_POST['role'] : 'participant';
        (new User())->updateUser($id,$name,$email,$password,$role);
        $this->redirect('users');
    }
    public function delete() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        (new User())->deleteUser($id);
        $this->redirect('users');
    }
    public function toggleBlock() {
        Auth::requireRole(['admin']);
        Auth::verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $u = (new User())->find($id);
        if ($u) {
            $new = $u['blocked'] ? 0 : 1;
            Database::getConnection()->prepare("UPDATE users SET blocked=? WHERE id=?")->execute([$new,$id]);
        }
        $this->redirect('users');
    }
}