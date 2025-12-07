<?php
class Users {
    public $conn;

    public function __construct() {
        $host = "localhost";
        $dbname = "beach_employee_system";
        $username = "root";
        $password = "";

        $this->conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function signup($data) {
    try {
        $q = $this->conn->prepare("
            SELECT e.employee_id 
            FROM employees e 
            LEFT JOIN users u ON e.employee_id = u.employee_id 
            WHERE e.email = ? OR u.username = ?
        ");
        $q->execute([$data['email'], $data['username']]);
        if ($q->rowCount() > 0) return 3;  // Email or username already exists

        $d = $this->conn->prepare("SELECT department_id FROM departments WHERE department_id = ?");
        $d->execute([$data['department_id']]);
        if ($d->rowCount() == 0) return 4; // Invalid department

        $this->conn->beginTransaction();

        $emp = $this->conn->prepare("
            INSERT INTO employees 
                (first_name, last_name, email, contact_number, department_id, date_hired, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $emp->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['contact_number'],
            $data['department_id'],
            $data['date_hired'],
            $data['status']
        ]);

        $employee_id = $this->conn->lastInsertId();

        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $u = $this->conn->prepare("
            INSERT INTO users (employee_id, username, password, role)
            VALUES (?, ?, ?, ?)
        ");
        $ok = $u->execute([
            $employee_id,
            $data['username'],
            $password,
            $data['role']
        ]);

        $this->conn->commit();
        return $ok ? 1 : 0;

    } catch (PDOException $e) {
        $this->conn->rollBack();
        return 0; 
    }
}


    public function login($username, $password) {
        try {
            $q = $this->conn->prepare("SELECT * FROM users WHERE username=?");
            $q->execute([$username]);
            $user = $q->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $u = $this->conn->prepare("UPDATE users SET last_login=NOW() WHERE user_id=?");
                $u->execute([$user['user_id']]);
                return ['success' => true, 'user_data' => $user];
            }

            return ['success' => false];

        } catch (PDOException $e) {
            return ['success' => false];
        }
    }

    public function adminLogin($username, $password) {
        $q = $this->conn->prepare("SELECT * FROM users WHERE username=? AND role='admin'");
        $q->execute([$username]);
        $admin = $q->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return ['success' => true, 'user_data' => $admin];
        }
        return ['success' => false];
    }

    public function updateProfile($user_id, $data) {
        try {
            $u = $this->conn->prepare("SELECT user_id, employee_id FROM users WHERE user_id=?");
            $u->execute([$user_id]);
            if ($u->rowCount() == 0) return 2;

            $user = $u->fetch(PDO::FETCH_ASSOC);
            $employee_id = $user['employee_id'];

            $e1 = $this->conn->prepare("SELECT employee_id FROM employees WHERE email=? AND employee_id!=?");
            $e1->execute([$data['email'], $employee_id]);
            if ($e1->rowCount() > 0) return 3;

            $u1 = $this->conn->prepare("SELECT user_id FROM users WHERE username=? AND user_id!=?");
            $u1->execute([$data['username'], $user_id]);
            if ($u1->rowCount() > 0) return 4;

            $d = $this->conn->prepare("SELECT department_id FROM departments WHERE department_id=?");
            $d->execute([$data['department_id']]);
            if ($d->rowCount() == 0) return 5;

            $this->conn->beginTransaction();

            $emp = $this->conn->prepare("
                UPDATE employees SET
                    first_name=?, last_name=?, department_id=?, email=?, 
                    contact_number=?, date_hired=?, status=?
                WHERE employee_id=?
            ");
            $emp->execute([
                $data['first_name'],
                $data['last_name'],
                $data['department_id'],
                $data['email'],
                $data['contact_number'],
                $data['date_hired'],
                $data['status'],
                $employee_id
            ]);

            if (!empty($data['password'])) {
                $password = password_hash($data['password'], PASSWORD_DEFAULT);
                $u2 = $this->conn->prepare("UPDATE users SET username=?, password=? WHERE user_id=?");
                $u2->execute([$data['username'], $password, $user_id]);
            } else {
                $u2 = $this->conn->prepare("UPDATE users SET username=? WHERE user_id=?");
                $u2->execute([$data['username'], $user_id]);
            }

            $this->conn->commit();
            return 1;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return 0;
        }
    }

    public function updateAccount($user_id, $data) {
        try {
            $u = $this->conn->prepare("SELECT user_id, employee_id FROM users WHERE user_id=?");
            $u->execute([$user_id]);

            if ($u->rowCount() == 0) {
                return ['success' => false, 'message' => 'User not found.'];
            }

            $user = $u->fetch(PDO::FETCH_ASSOC);
            $employee_id = $user['employee_id'];

            $e1 = $this->conn->prepare("SELECT employee_id FROM employees WHERE email=? AND employee_id!=?");
            $e1->execute([$data['email'], $employee_id]);
            if ($e1->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already in use.'];
            }

            $emp = $this->conn->prepare("UPDATE employees SET email=?, contact_number=? WHERE employee_id=?");
            $emp->execute([$data['email'], $data['contact_number'], $employee_id]);

            if (!empty($data['password'])) {
                $password = password_hash($data['password'], PASSWORD_DEFAULT);
                $u2 = $this->conn->prepare("UPDATE users SET password=? WHERE user_id=?");
                $u2->execute([$password, $user_id]);
            }

            return ['success' => true, 'message' => 'Account updated successfully!'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error.'];
        }
    }

    public function getEmployee($employee_id) {
        $q = $this->conn->prepare("SELECT * FROM employees WHERE employee_id=?");
        $q->execute([$employee_id]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function getUser($user_id) {
        $q = $this->conn->prepare("SELECT * FROM users WHERE user_id=?");
        $q->execute([$user_id]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }
}
?>