<?php
require_once(__DIR__ . '/../core/Database.php');

class ActivityLog extends Database {

    public function addLog($userEmail, $actionType, $recordId, $searchKeywords = null) {
        try {
            $stmt = $this->connect()->prepare("INSERT INTO activity_logs (user_email, action_type, record_id, search_keywords)
                                            VALUES (?, ?, ?, ?)");
            $stmt->execute([$userEmail, $actionType, $recordId, $searchKeywords]);

        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getActivityLogs() {
        try {
            $stmt = $this->connect()->prepare("SELECT al.*, u.email AS user_email 
                                               FROM activity_logs al 
                                               JOIN users u ON al.user_email = u.email 
                                               ORDER BY al.action_timestamp DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
}
?>