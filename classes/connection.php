<?php
// --- connection.php ---

class Dbh
{
    private $host = "localhost";
    private $user = "root"; // Check this
    private $pwd = "";      // Check this
    private $dbName = "beach_employee_system";
    protected $conn;

    public function connect()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pwd, $this->dbName);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
?>