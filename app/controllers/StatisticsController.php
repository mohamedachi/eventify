<?php
class StatisticsController extends Controller {
    
    public function dashboard() {
        Auth::requireRole(['admin', 'organizer']);
        
        // Statistiques générales
        $generalStats = $this->getGeneralStats();
        
        // Statistiques de participation par date
        $participationStats = $this->getParticipationByDate();
        
        // Statistiques de revenus
        $revenueStats = $this->getRevenueStats();
        
        $this->view('statistics/dashboard', [
            'generalStats' => $generalStats,
            'participationStats' => $participationStats,
            'revenueStats' => $revenueStats
        ], 'back');
    }
    
    public function participationChart() {
        Auth::requireRole(['admin', 'organizer']);
        
        $period = $_GET['period'] ?? 'daily';
        $data = $this->getParticipationByDate($period);
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public function revenueChart() {
        Auth::requireRole(['admin', 'organizer']);
        
        $period = $_GET['period'] ?? 'daily';
        $data = $this->getRevenueByDate($period);
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    private function getGeneralStats() {
        $db = Database::getConnection();
        
        $stmt = $db->query("
            SELECT 
                COUNT(DISTINCT e.id) as total_events,
                COUNT(DISTINCT p.id) as total_participations,
                COUNT(DISTINCT u.id) as total_users,
                COALESCE(SUM(e.price * participation_count.count), 0) as total_revenue
            FROM events e
            LEFT JOIN (
                SELECT event_id, COUNT(*) as count 
                FROM participations 
                GROUP BY event_id
            ) participation_count ON e.id = participation_count.event_id
            LEFT JOIN participations p ON e.id = p.event_id
            LEFT JOIN users u ON u.role = 'participant'
        ");
        
        return $stmt->fetch();
    }
    
    private function getParticipationByDate($period = 'daily') {
        $db = Database::getConnection();
        
        switch($period) {
            case 'weekly':
                $sql = "
                    SELECT 
                        YEARWEEK(p.created_at) as period,
                        DATE(DATE_SUB(p.created_at, INTERVAL WEEKDAY(p.created_at) DAY)) as date_label,
                        COUNT(*) as participations,
                        COUNT(DISTINCT p.event_id) as events_with_participation
                    FROM participations p
                    GROUP BY YEARWEEK(p.created_at)
                    ORDER BY period DESC
                    LIMIT 12
                ";
                break;
            case 'monthly':
                $sql = "
                    SELECT 
                        CONCAT(YEAR(p.created_at), '-', LPAD(MONTH(p.created_at), 2, '0')) as period,
                        CONCAT(YEAR(p.created_at), '-', LPAD(MONTH(p.created_at), 2, '0'), '-01') as date_label,
                        COUNT(*) as participations,
                        COUNT(DISTINCT p.event_id) as events_with_participation
                    FROM participations p
                    GROUP BY YEAR(p.created_at), MONTH(p.created_at)
                    ORDER BY YEAR(p.created_at) DESC, MONTH(p.created_at) DESC
                    LIMIT 12
                ";
                break;
            default: // daily
                $sql = "
                    SELECT 
                        DATE(p.created_at) as period,
                        DATE(p.created_at) as date_label,
                        COUNT(*) as participations,
                        COUNT(DISTINCT p.event_id) as events_with_participation
                    FROM participations p
                    WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(p.created_at)
                    ORDER BY DATE(p.created_at) DESC
                ";
                break;
        }
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
    
    private function getRevenueStats() {
        $db = Database::getConnection();
        
        $stmt = $db->query("
            SELECT 
                e.title,
                e.price,
                COUNT(p.id) as participants,
                (e.price * COUNT(p.id)) as total_revenue,
                e.event_date
            FROM events e
            LEFT JOIN participations p ON e.id = p.event_id
            WHERE e.status = 'published'
            GROUP BY e.id
            ORDER BY total_revenue DESC
            LIMIT 10
        ");
        
        return $stmt->fetchAll();
    }
    
    private function getRevenueByDate($period = 'daily') {
        $db = Database::getConnection();
        
        switch($period) {
            case 'weekly':
                $sql = "
                    SELECT 
                        YEARWEEK(p.created_at) as period,
                        DATE(DATE_SUB(p.created_at, INTERVAL WEEKDAY(p.created_at) DAY)) as date_label,
                        SUM(e.price) as revenue,
                        COUNT(p.id) as tickets_sold
                    FROM participations p
                    JOIN events e ON p.event_id = e.id
                    GROUP BY YEARWEEK(p.created_at)
                    ORDER BY period DESC
                    LIMIT 12
                ";
                break;
            case 'monthly':
                $sql = "
                    SELECT 
                        CONCAT(YEAR(p.created_at), '-', LPAD(MONTH(p.created_at), 2, '0')) as period,
                        CONCAT(YEAR(p.created_at), '-', LPAD(MONTH(p.created_at), 2, '0'), '-01') as date_label,
                        SUM(e.price) as revenue,
                        COUNT(p.id) as tickets_sold
                    FROM participations p
                    JOIN events e ON p.event_id = e.id
                    GROUP BY YEAR(p.created_at), MONTH(p.created_at)
                    ORDER BY YEAR(p.created_at) DESC, MONTH(p.created_at) DESC
                    LIMIT 12
                ";
                break;
            default: // daily
                $sql = "
                    SELECT 
                        DATE(p.created_at) as period,
                        DATE(p.created_at) as date_label,
                        SUM(e.price) as revenue,
                        COUNT(p.id) as tickets_sold
                    FROM participations p
                    JOIN events e ON p.event_id = e.id
                    WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(p.created_at)
                    ORDER BY DATE(p.created_at) DESC
                ";
                break;
        }
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function exportParticipationReport() {
        Auth::requireRole(['admin', 'organizer']);
        
        $format = $_GET['format'] ?? 'csv';
        $period = $_GET['period'] ?? 'daily';
        
        $data = $this->getParticipationByDate($period);
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="participation_report_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Période', 'Date', 'Participations', 'Événements']);
            
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['period'],
                    $row['date_label'],
                    $row['participations'],
                    $row['events_with_participation']
                ]);
            }
            
            fclose($output);
            exit;
        }
    }
    
    public function exportRevenueReport() {
        Auth::requireRole(['admin', 'organizer']);
        
        $format = $_GET['format'] ?? 'csv';
        $period = $_GET['period'] ?? 'daily';
        
        $data = $this->getRevenueByDate($period);
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="revenue_report_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Période', 'Date', 'Revenus', 'Tickets Vendus']);
            
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['period'],
                    $row['date_label'],
                    $row['revenue'],
                    $row['tickets_sold']
                ]);
            }
            
            fclose($output);
            exit;
        }
    }
}

