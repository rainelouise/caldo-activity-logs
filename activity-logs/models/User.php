<?php
require_once(__DIR__ . '/../core/Database.php');



class User extends Database {

    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function validatePassword($password) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    private function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9]{3,50}$/', $username);
    }

    public function register($username, $email, $password, $confirmPassword) {
        if ($password !== $confirmPassword) {
            return "Passwords do not match.";
        }
    
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long.";
        }
    
        try {
            $stmt = $this->connect()->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $passwordHash = password_hash($password, PASSWORD_DEFAULT); 
            $stmt->execute([$username, $email, $passwordHash]);
            return "Registration successful!";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { 
                return "Username or email already exists.";
            }
            return "An error occurred: " . $e->getMessage();
        }
    }
    
    public function login($usernameOrEmail, $password) {
        try {
            if (!$this->validateEmail($usernameOrEmail) && !$this->validateUsername($usernameOrEmail)) {
                return "Invalid username or email format.";
            }

            $conn = $this->connect();
            $sql = "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':usernameOrEmail' => $usernameOrEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return "Login successful!";
            } else {
                return "Invalid credentials!";
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

     public function logout() {
        session_start();
        session_unset();  
        session_destroy();  
    }
}