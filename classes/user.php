<?php
include_once __DIR__ . "/Db.php";
class User
{
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    private $role;
    public $id;
    private $currency;

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

    //     public function setRole($role)
    public function setRole($role)
    {
        if (empty($role)) {
            throw new Exception('Role cannot be empty');
        }
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }
    //get id
    public function getId()
    {
        return $this->id;
    }

    public function save()
    {
        // Set the default role to 0 if not already set
        if (empty($this->role)) {
            $this->role = 0; // Default to customer role
        }

        // Connect to the database
        $conn = Db::getConnection();

        // Prepare the SQL query, including the `currency` column
        $statement = $conn->prepare(
            "INSERT INTO users (firstname, lastname, email, password, role, currency) 
             VALUES (:firstname, :lastname, :email, :password, :role, :currency)"
        );

        // Bind the parameters
        $statement->bindValue(':firstname', $this->getFirstname());
        $statement->bindValue(':lastname', $this->getLastname());
        $statement->bindValue(':email', $this->getEmail());
        $statement->bindValue(':password', $this->getPassword());
        $statement->bindValue(':role', $this->getRole()); // Bind the role
        $statement->bindValue(':currency', $this->getCurrency()); // Bind the default currency

        // Execute the query
        $result = $statement->execute();

        // Set the id property of the user object
        if ($result) {
            $this->id = $conn->lastInsertId();
        }

        return $result;
    }


    public function updateCurrency($amount)
    {
        $conn = Db::getConnection();

        // Update the user's currency
        $statement = $conn->prepare("UPDATE users SET currency = :currency WHERE email = :email");
        $statement->bindValue(':currency', $amount);
        $statement->bindValue(':email', $this->getEmail());
        $result = $statement->execute();

        if (!$result) {
            throw new Exception('Valuta bijwerken mislukt.');
        }

        $this->currency = $amount; // Update the object property
        return true;
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
            // Set session with user data (including role)
            $_SESSION['user'] = $user; // Store user info in session
            $_SESSION['role'] = $user['role']; // Store role in session
            $_SESSION['currency'] = $user['currency']; // Store currency in session
            return true; // Login successful
        }

        return false; // Incorrect password
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



    public static function getById($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $userObj = new User();
            $userObj->id = $user['id'];
            $userObj->firstname = $user['firstname'];
            $userObj->lastname = $user['lastname'];
            $userObj->email = $user['email'];
            return $userObj;
        }

        return null;
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
    public function setCurrency($currency)
    {
        if (!is_numeric($currency) || $currency < 0) {
            throw new Exception('Currency must be a non-negative number');
        }
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

}


?>