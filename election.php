<?php


session_start();

if(isset($_SESSION['user_email'],$_SESSION['user_is_logged_in'],$_SESSION['user_name']))
{

}
else
{
	header("Location:login.php");
}

class Election {

  private $db_connection = null;

  public $feedback = "";

  public function __construct()
  {
    $this->start();
  }

  public function start()
  {

    $this->createDatabaseConnection();
    if($this->verifyUser())
    {

    }
    else {
      header('location:main.php');
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

  private function verifyUser()
  {

    $sql = 'SELECT name, description, id, campus, image_path, longdesc
            FROM election
            WHERE id = :id';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':id', $_GET['election']);
    $query->execute();
    print_r($this->db_connection->errorInfo());
    $results = $query->fetchObject();
    if($results->campus === $_SESSION['user_campus'] || $results->campus === "all" )
    {
      return true;

    }
    return false;
  }


}

$application = new Election();
?>
