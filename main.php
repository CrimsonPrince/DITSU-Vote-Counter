<?php

session_start();

if(isset($_SESSION['user_email'],$_SESSION['user_is_logged_in'],$_SESSION['user_name']))
{

}
else
{
	header("Location:login.html");
}


class Display_elections
{

  private $db_connection = null;

  public $feedback = "";

  public function __construct()
  {
    $this->start();
  }

  public function start()
  {



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






}


$application = new Display_elections();
 ?>
