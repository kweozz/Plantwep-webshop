<?php
include_once(__DIR__ ."/Db.php");
class User {
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    //private $role;

    public function setFirstname($firstname) {
        //if empty firstname create new exception
        if (empty($firstname)) {
            throw new Exception('Firstname cannot be empty');
        }
        $this->firstname = $firstname;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function setLastname($lastname) {
        //if empty lastname create new exception
        if (empty($lastname)) {
            throw new Exception('Lastname cannot be empty');
        }
        $this->lastname = $lastname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function setEmail($email) {
        //if empty email create new exception
        if (empty($email)) {
            throw new Exception('Email cannot be empty');
        }
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setPassword($password) {
        if (empty($password)) {
            throw new Exception('Password cannot be empty');
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    }

    public function getPassword() {
        return $this->password;
    }


   /* public function setRole($role) {
        if (empty($role)) {
            throw new Exception('Role cannot be empty');
        }   
        $this->role = $role;
    }

    public function getRole() {
        return $this->role;
    }*/

    //save function
    public function save() {
        //connect to the database
       $conn = Db::getConnection();
        //insert query into user table (firstname, lastname, email, password)
        $statement = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
    
        //bind the parameters
        $firstname = $this->getFirstname();   
        $lastname = $this->getLastname();
        $email = $this->getEmail();
        $password = $this->getPassword();
        //$role = $this->getRole();
        //bind the parameters
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $password);
        //$statement->bindValue(':role', $role);

        $result = $statement->execute();
        
        return $result;
    }
   //public static function login
   public static function login($email, $password) {
    $conn = Db::getConnection();
    $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $statement->bindValue(':email', $email);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            return $user; // Login successful
        } else {
            throw new Exception("Invalid password.");
        }
    } else {
        throw new Exception("No user found with this email.");
    }
}

    public static function getAll() {
        $conn = Db::getConnection();
        $statement = $conn->prepare('SELECT * FROM users');
        $statement->execute();
        $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}

?>