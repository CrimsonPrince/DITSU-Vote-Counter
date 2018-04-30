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
    if(isset($_POST['candidate1']))
    {
      $this->createDatabaseConnection();
      $this->countcandidates();

    }
    else
    {

            $this->createDatabaseConnection();
            if($this->verifyUser())
            {
              $this->displaycandidates();
            }
            else {
              header('location:main.php');
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

  private function verifyUser()
  {

    $sql = 'SELECT campus
            FROM election
            WHERE id = :id';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':id', $_GET['election']);
    $query->execute();
    $results = $query->fetchObject();
    if($results->campus === $_SESSION['user_campus'] || $results->campus === "all" )
    {
      return true;

    }
    return false;
  }

  private function displaycandidates()
  {
    $sql = 'SELECT name, id, image_path, manifesto, election_id
            FROM candidates
            WHERE election_id = :id';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':id', $_GET['election']);
    $query->execute();
    $results = $query->fetchAll();
    $total = 0;
    foreach($results as $result)
    {
      $total++;
    }
    $count = 0;

      include("top.php");
      echo '<div class="container row">';
      echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" name="electionform">';
      foreach($results as $result)
      {
          echo '<div class="col s12 m4">';
              echo '<div class="card sticky-action" style="overflow: visible;">';
                echo '<div class="card-image waves-effect waves-block waves-light">';
                  echo '<img class="activator" src=' . $result['image_path'] . " style='height:25vh;' >";
                echo '</div>';
                echo '<div class="card-content">';
                  echo '<span class="card-title activator grey-text text-darken-4">' . $result['name'] . '<i class="material-icons right">more_vert</i></span>';

                  echo '<p> Read My Manifesto </p>';
              echo '</div>';

                echo '<div class="card-action">';
                echo '<label for="login_input_username" class="blue-text">Enter Prefrence 1,2,3...</label> ';
                echo '<input id="login_input_username" type="number" min="1" max="' . $total. '" name="candidate' . $count++ . '" /> ';
                echo '</div>';
                echo '<div class="card-reveal" style="display: none; transform: translateY(0%);">';
                  echo '<span class="card-title grey-text text-darken-4">' . $result['name'] . '<i class="material-icons right">close</i></span>';
                  echo '<p>'. $result['manifesto'] . '</p>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
    }
    echo '<input type="hidden" name="id" value="' . $_GET['election'] . '"/>';
    echo '<input class="btn btn-large waves-effects blue col s3 offset-s2" type="submit"  name="login" value="Log in" />';
    echo '</div>';
    include("bottom.php");

    if(isset($_GET['error']))
    {
      echo "<script>M.toast({html:'Please Enter Unique Values for your vote'}, 3000, 'rounded');</script>";
    }
  }

  private function countcandidates()
  {
    $sql = 'SELECT name, id, image_path, manifesto, election_id
            FROM candidates
            WHERE election_id = :id';
    $query = $this->db_connection->prepare($sql);
    $query->bindValue(':id', $_POST['id']);
    $query->execute();
    $results = $query->fetchAll();
    $data = array();
    $i = 0;

      foreach($results as $result)
      {
        $data += [$result['id'] => $_POST['candidate' . $i]];
        echo $_POST['candidate' . $i];
        $i++;
      }

    if(count($data) !== count(array_unique($data)))
    {
      header("location:election.php?error=1&election=" . $_POST['id']);
    }

  }

}

$application = new Election();
?>
