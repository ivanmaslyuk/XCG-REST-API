<?php


class UserModel
{
    private $conn;
    private $tableName = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $onlineStatus;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getById() {
        $query = "SELECT username, password, email, online_status
                FROM " . $this->tableName ."
                WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $u = $stmt->fetch();

        $this->username = $u["username"];
        $this->password = $u["password"];
        $this->email = $u["email"];
        $this->onlineStatus = $u["online_status"];
    }

    public function save() {
        $query = "UPDATE " .$this->tableName. " 
                    SET username = :username,
                    password = :password,
                    email = :email,
                    online_status = :onlineStatus 
                    WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->onlineStatus = htmlspecialchars(strip_tags($this->onlineStatus));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':onlineStatus', $this->onlineStatus);
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();
    }
}