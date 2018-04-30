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

      echo $numvotes / $candidatecount + 1;

      $sql = 'SELECT serialized
              FROM vote
              WHERE id = :id';
      $query = $this->db_connection->prepare($sql);
      $query->bindValue(':id', $_GET['id']);
      $query->execute();
      $results = $query->fetchAll();

      foreach($results as $result)
      {
        $data = unserialize($result['serialized']);
        print_r($data);
        echo '<br>';
      }
    }


}


$application = new VoteCount;
?>
