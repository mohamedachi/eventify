<?php
class AuthController extends Controller {
    public function login() { $this->view('auth/login'); }
    public function register() { $this->view('auth/register'); }
    public function handleLogin() {
        Auth::verifyCsrf();
        require_once __DIR__ . '/../../core/Captcha.php';
        if (!Captcha::verify($_POST['g-recaptcha-response'] ?? null)) { $this->view('auth/login', ['error'=>'reCAPTCHA failed']); return; }
        $email = $this->input('email');
        $password = $_POST['password'] ?? '';
        $user = (new User())->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', ['error'=>'Invalid credentials']);
            return;
        }
        if (!empty($user['blocked'])) { $this->view('auth/login', ['error'=>'Your account is blocked. Contact admin.']); return; }
        Auth::login($user);
        $this->redirect('dashboard');
    }
    public function handleRegister() {
        Auth::verifyCsrf();
        $name = $this->input('name'); $email = $this->input('email');
        $password = $_POST['password'] ?? '';
        $role = in_array($_POST['role'] ?? 'participant', ['admin','organizer','participant']) ? $_POST['role'] : 'participant';
        $um = new User();
        if ($um->findByEmail($email)) {
            $this->view('auth/register', ['error'=>'Email already in use']); return;
        }
        $id = $um->create($name,$email,$password,$role);
        $user = $um->find($id);
        Auth::login($user);
        $this->redirect('dashboard');
    }

    public function requestPasswordReset() {
        $this->view('auth/request_reset');
    }
    public function sendPasswordReset() {
        Auth::verifyCsrf();
        $email = $this->input('email');
        $um = new User();
        $user = $um->findByEmail($email);
        if (!$user) { $this->view('auth/request_reset', ['message'=>'If the email exists, a reset link has been generated']); return; }
        $token = bin2hex(random_bytes(32));
        $stmt = Database::getConnection()->prepare("INSERT INTO password_resets (email, token) VALUES (?,?)");
        $stmt->execute([$email, $token]);
        $link = base_url('reset_password?token='.$token);
        $this->view('auth/request_reset', ['message'=>'Reset link (dev): '.htmlspecialchars($link)]);
    }
    public function resetPasswordForm() {
        $token = $this->input('token');
        $this->view('auth/reset_password', ['token'=>$token]);
    }
    public function performResetPassword() {
        Auth::verifyCsrf();
        $token = $this->input('token'); $password = $_POST['password'] ?? '';
        $stmt = Database::getConnection()->prepare("SELECT email FROM password_resets WHERE token=? LIMIT 1");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if (!$row) { $this->view('auth/reset_password', ['error'=>'Invalid or expired token']); return; }
        $email = $row['email'];
        $um = new User();
        $user = $um->findByEmail($email);
        if (!$user) { $this->view('auth/reset_password', ['error'=>'User not found']); return; }
        $stmt2 = Database::getConnection()->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt2->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        Database::getConnection()->prepare("DELETE FROM password_resets WHERE token=?")->execute([$token]);
        $this->redirect('login');
    }

    public function logout() {
        Auth::verifyCsrf();
        Auth::logout();
        $this->redirect('login');
    }
}