<?php
// classes/Admin.php

class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        if ($this->conn instanceof mysqli) {
            $this->createDefaultAdmin(); 
        }
    }

    private function createDefaultAdmin() {
        $username = 'superadmin'; 
        $password = password_hash('admin12345', PASSWORD_DEFAULT); 
        $email = 'default@admin.com'; 

        if (!$this->conn) return;

        try {
            $stmt = $this->conn->prepare("SELECT admin_id FROM admin WHERE username = ?");
            if (!$stmt) {
                error_log("Admin table query failed: " . $this->conn->error);
                return; 
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt_insert = $this->conn->prepare(
                    "INSERT INTO admin (username, password, email, last_login) VALUES (?, ?, ?, NULL)"
                );
                $stmt_insert->bind_param("sss", $username, $password, $email);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
            $stmt->close();
        } catch (\Exception $e) {
            error_log("CRITICAL: Admin table missing or bad query: " . $e->getMessage());
        }
    }

    public function login($username, $password) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!$this->conn) return false; 

        $stmt = $this->conn->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
        if (!$stmt) return false; 
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            $stmt_upd = $this->conn->prepare("UPDATE admin SET last_login = NOW() WHERE admin_id = ?");
            $stmt_upd->bind_param("i", $admin['admin_id']);
            $stmt_upd->execute();
            $stmt_upd->close();

            return true;
        }
        return false;
    }

    public function deleteEmployee($employeeId) {
        if (!$this->conn || !is_numeric($employeeId)) {
            return false;
        }

        $stmt = $this->conn->prepare("DELETE FROM employees WHERE employee_id = ?");
        
        if (!$stmt) {
            error_log("Delete query preparation failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $employeeId); 
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
    
    public function getAllEmployees() {
        if (!$this->conn) return [];
        $query = "SELECT e.*, d.department_name 
                  FROM employees e 
                  JOIN departments d ON e.department_id = d.department_id 
                  ORDER BY e.employee_id DESC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAllAttendance() {
        if (!$this->conn) return [];
        $query = "SELECT a.*, e.first_name, e.last_name 
                  FROM attendance a 
                  JOIN employees e ON a.employee_id = e.employee_id 
                  ORDER BY a.created_at DESC";
        $result = $this->conn->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>