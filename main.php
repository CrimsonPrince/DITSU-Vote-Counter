<?php

session_start();

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


    $sql = 'SELECT name, description, id, campus, image_path, longdesc
            FROM election
            WHERE campus = :user_campus OR campus = :no_campus';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':user_campus', $_SESSION['user_campus']);
    $query->bindValue(':no_campus', 'all');
    $query->execute();
    $results = $query->fetchAll();
    if ($results) {
      include("top.php");
      echo '<div class="container row">';
      foreach($results as $result)
      {
          echo '<div class="col s12 m4">';
              echo '<div class="card sticky-action" style="overflow: visible;">';
                echo '<div class="card-image waves-effect waves-block waves-light">';
                  echo '<img class="activator" src=' . $result['image_path'] . " style='height:25vh;' >";
                echo '</div>';
                echo '<div class="card-content">';
                  echo '<span class="card-title activator grey-text text-darken-4">' . $result['name'] . '<i class="material-icons right">more_vert</i></span>';

                  echo '<p>' . $result['description'] . '</p>';
              echo '</div>';

                echo '<div class="card-action">';
                  echo '<a href="election.php?election=' . $result['id'] . '">Vote Now</a>';
                echo '</div>';

                echo '<div class="card-reveal" style="display: none; transform: translateY(0%);">';
                  echo '<span class="card-title grey-text text-darken-4">' . $result['name'] . '<i class="material-icons right">close</i></span>';
                  echo '<p>'. $result['longdesc'] . '</p>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
    }
    echo '</div>';
    include("bottom.php");

    }
    else {
      $this->feedback = "Database Error, please login in again";
    }


  }

}


$application = new Display_elections();

?>
