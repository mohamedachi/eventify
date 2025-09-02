<?php
class DashboardController extends Controller {
    public function index() {
        Auth::requireRole(['admin','organizer','participant']);
        $role = Auth::role();
        $eventModel = new Event();
        if ($role === 'admin') {
            $stats = $eventModel->stats();
            $this->view('dashboard/admin', ['stats'=>$stats], 'back');
        } elseif ($role === 'organizer') {
            $events = $eventModel->byUser(Auth::id());
            $this->view('dashboard/organizer', ['events'=>$events], 'back');
        } else {
            $joined = (new Participation())->byUser(Auth::id());
            $this->view('dashboard/participant', ['events'=>$joined]);
        }
    }
}