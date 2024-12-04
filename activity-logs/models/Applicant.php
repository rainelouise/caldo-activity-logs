<?php
require_once(__DIR__ . '/../core/Database.php');
require_once(__DIR__ . '/../models/ActivityLog.php');  

class Applicant extends Database {

    public function getApplicantById($id) {
        $stmt = $this->connect()->prepare("SELECT * FROM applicants WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getUserEmailById($userId) {
        $stmt = $this->connect()->prepare("SELECT email FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['email'] ?? null;
    }

    public function getAllApplicants($userId) {
        try {
            $stmt = $this->connect()->prepare("SELECT * FROM applicants");
            $stmt->execute();
            $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $userEmail = $this->getUserEmailById($userId);
            
            return $applicants;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteApplicant($id, $userId) {
        try {
            $stmt = $this->connect()->prepare("DELETE FROM applicants WHERE id = ?");
            $stmt->execute([$id]);

            $userEmail = $this->getUserEmailById($userId);

            $activityLogObj = new ActivityLog();
            $activityLogObj->addLog($userEmail, 'DELETE', $id);  

        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function createApplicant($username, $first_name, $last_name, $birth_date, $gender, $email_address, $phone_number, $applied_position, $start_date, $address, $nationality, $userId) {
        try {
            $stmt = $this->connect()->prepare("INSERT INTO applicants (username, first_name, last_name, birth_date, gender, email_address, phone_number, applied_position, start_date, address, nationality)
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
            $stmt->execute([$username, $first_name, $last_name, $birth_date, $gender, $email_address, $phone_number, $applied_position, $start_date, $address, $nationality]);
    
            $stmt = $this->connect()->prepare("SELECT id FROM applicants WHERE username = ? AND email_address = ?");
            $stmt->execute([$username, $email_address]);
    
            $applicant = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastInsertId = $applicant['id'];
    
            if (!$lastInsertId) {
                throw new Exception("Failed to retrieve the last inserted applicant ID.");
            }
    
            $userEmail = $this->getUserEmailById($userId);
    
            $activityLogObj = new ActivityLog();
            $activityLogObj->addLog($userEmail, 'INSERT', $lastInsertId);  
    
            return $lastInsertId;  
    
        } catch (PDOException $e) {
            return "Database Error: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updateApplicant($id, $username, $first_name, $last_name, $email_address, $phone_number, $applied_position, $start_date, $address, $nationality, $userId) {
        try {
            $stmt = $this->connect()->prepare("UPDATE applicants 
                                               SET username = ?, first_name = ?, last_name = ?, email_address = ?, phone_number = ?, applied_position = ?, start_date = ?, address = ?, nationality = ?
                                               WHERE id = ?");
            $stmt->execute([$username, $first_name, $last_name, $email_address, $phone_number, $applied_position, $start_date, $address, $nationality, $id]);

            $userEmail = $this->getUserEmailById($userId);

            $activityLogObj = new ActivityLog();
            $activityLogObj->addLog($userEmail, 'UPDATE', $id);  

        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function searchApplicants($searchTerm, $userId) {
        try {
            $searchTerm = "%" . $searchTerm . "%";
            $stmt = $this->connect()->prepare("SELECT * FROM applicants WHERE username LIKE ? OR first_name LIKE ? OR last_name LIKE ?");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $userEmail = $this->getUserEmailById($userId);

            $activityLogObj = new ActivityLog();
            $activityLogObj->addLog($userEmail, 'SEARCH', null, $searchTerm);

            return $results;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
?>