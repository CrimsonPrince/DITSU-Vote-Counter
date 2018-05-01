<?php

 session_start();

if(isset($_SESSION['user_email'],$_SESSION['user_is_logged_in'],$_SESSION['user_name']))
{

}
else
{
 header("Location:login.php");
}

class VoteCount
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
      $this->countVotes();

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

    private function countVotes()
    {
      $candidatecount = $this->db_connection->query('select count(*) from candidates where election_id =' . $_GET['id'] . '')->fetchColumn();

      $numvotes = $this->db_connection->query('select count(*) from vote')->fetchColumn();
      $offset = $this->db_connection->query('select id from candidates where election_id =' . $_GET['id'] . ' LIMIT 1')->fetchColumn();
      $numseats = 2;

      $quota = $numvotes / $numseats;

      $sql = 'SELECT serialized
              FROM vote
              WHERE id = :id';
      $query = $this->db_connection->prepare($sql);
      $query->bindValue(':id', $_GET['id']);
      $query->execute();
      $results = $query->fetchAll();
      $count = array_fill(0,$candidatecount + $offset,0);

      foreach($results as $result)
      {
        $data = unserialize($result['serialized']);
        $key = array_search("1",$data,true);
        $count[$key] = $count[$key] + 1;
      }

      foreach($count as $key1 => $num)
      {
        if($num > $quota)
        {
          include("top.php");
          echo "<div class='container'>";
          $result = $this->db_connection->query('select * from candidates where id =' . $key . '')->fetchObject();

          echo '<div class="col s12 m4">';
              echo '<div class="card sticky-action" style="overflow: visible;">';
                echo '<div class="card-image waves-effect waves-block waves-light">';
                  echo '<img class="activator" src=' . $result->image_path . " style='height:70vh;' >";
                echo '</div>';
                echo '<div class="card-content">';
                  echo '<span class="card-title activator grey-text text-darken-4">' . $result->name . '<i class="material-icons right">more_vert</i></span>';

                  echo '<p>  Has been Deemed Elected by Popular vote </p>';
              echo '</div>';

                echo '<div class="card-action">';
                echo '</div>';
                echo '<div class="card-reveal" style="display: none; transform: translateY(0%);">';
                  echo '<span class="card-title grey-text text-darken-4">' . $result->name . '<i class="material-icons right">close</i></span>';
                  echo '<p>'. $result->manifesto . '</p>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
          echo "</div>";
          include("bottom.php");
        }
      }
    }


}


$application = new VoteCount;
?>
