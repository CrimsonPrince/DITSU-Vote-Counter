<?php

class Login
{
    private $config;
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $db_connection = null;

    private $user_is_logged_in = false;

    public $feedback = "";



    public function __construct()
    {
      $this->start();
    }


    public function start()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "register") {
            $this->doRegistration();
            $this->showPageRegistration();
        } else {

            $this->doStartSession();

            $this->performUserLoginAction();

            if ($this->getUserLoginStatus()) {
                $this->showPageLoggedIn();
            } else {
                $this->showPageLoginForm();
            }
        }
    }


    private function createDatabaseConnection()
    {

        $config = $config = parse_ini_file('config/config.ini');
        $servername = $config['servername'];
        $username = $config['username'];
        $password = $config['password'];
        $dbname = $config['dbname'];


        try {
            $this->db_connection = new PDO("mysql:host=$servername;dbname=$dbname", "$username", "$password");
            return true;
        } catch (PDOException $e) {
            $this->feedback = "PDO database connection problem: " . $e->getMessage();
        } catch (Exception $e) {
            $this->feedback = "General problem: " . $e->getMessage();
        }
        return false;
    }


    private function performUserLoginAction()
    {
        if (isset($_GET["action"]) && $_GET["action"] == "logout") {
            $this->doLogout();
        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST["login"])) {
            $this->doLoginWithPostData();
        }
    }


    private function doStartSession()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
    }


    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true;
    }


    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty()) {
            if ($this->createDatabaseConnection()) {
                $this->checkPasswordCorrectnessAndLogin();
            }
        }
    }


    private function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
        $this->feedback = "You were just logged out.";
    }


    private function doRegistration()
    {
        if ($this->checkRegistrationData()) {
            if ($this->createDatabaseConnection()) {
                $this->createNewUser();
            }
        }

        return false;
    }


    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Username field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->feedback = "Password field was empty.";
        }

        return false;
    }


    private function checkPasswordCorrectnessAndLogin()
    {

        $sql = 'SELECT user_name, user_email, user_password_hash
                FROM users
                WHERE user_name = :user_name OR user_email = :user_name
                LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $_POST['user_name']);
        $query->execute();


        $result_row = $query->fetchObject();
        if ($result_row) {

            if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {

                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_is_logged_in'] = true;
                $this->user_is_logged_in = true;
                return true;
            } else {
                $this->feedback = "Wrong password.";
            }
        } else {
            $this->feedback = "This user does not exist.";
        }

        return false;
    }


    private function checkRegistrationData()
    {

        $campus = array("Bolton St","Kevin St","Aungier St","Cathal Brugha St","Grangegorman","Rathmines/BIMM");

        if (!isset($_POST["register"])) {
            return false;
        }


        if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && strlen($_POST['user_password_new']) >= 6
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
            && !empty($_POST['user_campus'])
            && in_array($_POST['user_campus'], $campus , true)
        ) {

            return true;
        } elseif (empty($_POST['user_name'])) {
            $this->feedback = "Empty Username";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->feedback = "Empty Password";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->feedback = "Password and password repeat are not the same";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->feedback = "Password has a minimum length of 6 characters";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->feedback = "Username cannot be shorter than 2 or longer than 64 characters";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->feedback = "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
        } elseif (empty($_POST['user_email'])) {
            $this->feedback = "Email cannot be empty";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->feedback = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->feedback = "Your email address is not in a valid email format";
        } else {
            $this->feedback = "An unknown error occurred.";
        }



        return false;
    }


    private function createNewUser()
    {

        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $user_email = htmlentities($_POST['user_email'], ENT_QUOTES);
        $user_campus = htmlentities($_POST['user_campus'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];

        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = 'SELECT * FROM users WHERE user_name = :user_name OR user_email = :user_email';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':user_email', $user_email);
        $query->execute();


        $result_row = $query->fetchObject();
        if ($result_row) {
            $this->feedback = "Sorry, that username / email is already taken. Please choose another one.";
        } else {
            $sql = 'INSERT INTO users (user_name, user_password_hash, user_email, user_campus)
                    VALUES(:user_name, :user_password_hash, :user_email, :user_campus)';
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':user_name', $user_name);
            $query->bindValue(':user_password_hash', $user_password_hash);
            $query->bindValue(':user_email', $user_email);
            $query->bindValue(':user_campus', $user_campus);

            $registration_success_state = $query->execute();

            if ($registration_success_state) {
                $this->feedback = "Your account has been created successfully. You can now log in.";
                return true;
            } else {
                $this->feedback = "Sorry, your registration failed. Please go back and try again.";
            }
        }

        return false;
    }


    public function getUserLoginStatus()
    {
        return $this->user_is_logged_in;
    }


    private function showPageLoggedIn()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }

        header('vote.php');
        echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?action=logout">Log out</a>';
    }


    private function showPageLoginForm()
    {
        if ($this->feedback) {
            echo $this->feedback . "<br/><br/>";
        }

        include('top.php');
        echo '<br>';
        echo '<br>';
        echo '<div class="container grey lighten-2 z-depth-5">';
        echo '<div class="container row">';
        echo '<h2 class="blue-text">Login</h2>';
        echo '<br>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" name="loginform">';
        echo '<label for="login_input_username" class="blue-text">Username (or email)</label> ';
        echo '<input id="login_input_username" type="text" name="user_name" required /> ';
        echo '<br>';
        echo '<br>';
        echo '<label for="login_input_password" class="blue-text">Password</label> ';
        echo '<input id="login_input_password" type="password" name="user_password" required /> ';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<input class="btn btn-large waves-effects blue col s3 offset-s2" type="submit"  name="login" value="Log in" />';
        echo '</form>';

        echo '<a class="btn btn-large waves-effects blue col s3 offset-s2" href="' . $_SERVER['SCRIPT_NAME'] . '?action=register">Register</a>';
        echo '</div>';
        echo '<br>';
        echo '</div>';
        include('bottom.php');
    }


    private function showPageRegistration()
    {
        if ($this->feedback) {
            echo "<script> M.toast({html: " . $this->feedback . "}) </script>" . "<br><br>";
        }
        include('top.php');

        echo '<div class="container grey lighten-2 z-depth-5">';
        echo '<div class="container row">';
        echo '<h2 class="blue-text">Registration</h2>';
        echo '<br>';
        echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '?action=register" name="registerform">';
        echo '<label for="login_input_username"><h6 class="blue-text">Username (only letters and numbers, 2 to 64 characters)</h6></label>';
        echo '<input id="login_input_username" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" class="validate" required />';
        echo '<br>';
        echo '<label for="login_input_email"><h6 class="blue-text">User\'s email</h6></label>';
        echo '<input id="login_input_email" type="email" name="user_email" class="validate" required />';
        echo '<br>';
        echo '<label for="login_input_password_new"><h6 class="blue-text">Password (min. 6 characters)</h6></label>';
        echo '<input id="login_input_password_new" class="login_input" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />';
        echo '<br>';
        echo '<label for="login_input_password_repeat"><h6 class="blue-text">Repeat password</h6></label>';
        echo '<input id="login_input_password_repeat" class="login_input" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />';
        echo '<label for="campus"><h6 class="blue-text">Campus</h6></label>';
        echo '<div class="input-field">';
        echo '<select class="icons" id="campus" name="user_campus">';
        echo '<option value="" disabled selected>Choose your Campus</option>';
        echo '<option value="Aungier St" data-icon="images/DIT-Aungier-Street-750x500.jpg" class="left">Aungier St</option>';
        echo '<option value="Bolton St" data-icon="images/bolton.jpg" class="left">Bolton St</option>';
        echo '<option value="Cathal Brugha St" data-icon="images/CathalBrugha.jpg" class="left">Cathal Brugha St</option>';
        echo '<option value="Grangegorman" data-icon="images/Grangegorman.jpg" class="left">Grangegorman</option>';
        echo '<option value="Kevin St" data-icon="images/bd-kevinst-1.jpg" class="left">Kevin St</option>';
        echo '<option value="Rathmines/BIMM" data-icon="images/DSCN0702-670x790.jpg" class="left">Rathmines/BIMM</option>';
        echo '</select>';
        echo '</div>';
        echo '<br>';
        echo '<input type="submit"class="btn btn-large waves-effects blue col s3 offset-s2" name="register" value="Register" />';
        echo '</form>';

        echo '<a class="btn btn-large waves-effects blue col s3 offset-s2" href="' . $_SERVER['SCRIPT_NAME'] . '">Login</a>';
        echo '</div>';
        echo '<br>';
        echo '</div>';

        include('bottom.php');
    }
}

$application = new Login();
