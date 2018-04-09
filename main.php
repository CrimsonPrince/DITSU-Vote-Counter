<?php

session_start();

echo $_SESSION['user_campus'];
if(isset($_SESSION['user_email'],$_SESSION['user_is_logged_in'],$_SESSION['user_name']))
{

}
else
{
	header("Location:login.php");
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

    $this->createDatabaseConnection();
    $this->display_election_page();

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


  private function display_election_page()
  {
    $sql = 'SELECT name, description, id, campus, image_path
            FROM election
            WHERE campus = :user_campus
            LIMIT 1';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':user_campus', $_SESSION['user_campus']);
    $query->execute();


    $result_row = $query->fetchObject();
    if ($result_row) {

      include("top.php");



      include("bottom.php");

    }
    else {
      $this->feedback = "Database Error, please login in again";
    }


  }

}


$application = new Display_elections();

?>
