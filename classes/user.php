<?php
include_once(__DIR__ . "/Db.php");
class User
{
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    //private $role;

    public function setFirstname($firstname)
    {
        //if empty firstname create new exception
        if (empty($firstname)) {
            throw new Exception('Firstname cannot be empty');
        }
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        //if empty lastname create new exception
        if (empty($lastname)) {
            throw new Exception('Lastname cannot be empty');
        }
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setEmail($email)
    {
        //if empty email create new exception
        if (empty($email)) {
            throw new Exception('Email cannot be empty');
        }
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        if (empty($password)) {
            throw new Exception('Password cannot be empty');
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    }

    public function getPassword()
    {
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
    public function save()
    {
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
    public static function login($email, $password)
{
    $conn = Db::getConnection();
    $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $statement->bindValue(':email', $email);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false; // No user found with this email
    }


    // Verify the password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true; // Login successful
    }

    return false; // Incorrect password
    //logic to check if account exists
}
public static function exists($email)
{
    $conn = Db::getConnection();
    $statement = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $statement->bindValue(':email', $email);
    $statement->execute();
    
    // Fetch the count (if count > 0, user exists)
    $count = $statement->fetchColumn();
    
    return $count > 0; // Return true if user exists, false otherwise
}



    public static function getAll()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare('SELECT * FROM users');
        $statement->execute();
        $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
   
    public static function getUserByEmail($email)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $statement->bindValue(':email', $email);
        $statement->execute();
    
        $userData = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($userData) {
            $user = new self();
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setEmail($userData['email']);
            // Stel andere velden in indien nodig
            return $user;
        }
    
        throw new Exception("User not found");
    }
    public function changePassword($current_password, $new_password)
{
    // Check if new password is provided
    if (empty($new_password)) {
        throw new Exception('New password cannot be empty');
    }
    $conn = Db::getConnection(); // db conn!
    $statement = $conn->prepare("SELECT password FROM users WHERE email = :email"); // Get the password from the database
    $statement->bindValue(':email', $this->getEmail()); // Get the email from the current user
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current_password, $user['password'])) {
        throw new Exception('Current password is incorrect');
    }

    // Update password
    $this->password = password_hash($new_password, PASSWORD_DEFAULT);
    $statement = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
    $statement->bindValue(':password', $this->password);
    $statement->bindValue(':email', $this->getEmail());
    $result = $statement->execute();

    return $result;
}

}


?>