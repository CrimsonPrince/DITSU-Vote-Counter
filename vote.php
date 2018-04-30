<?php
   session_start();

   if(isset($_SESSION['user_email'],$_SESSION['user_is_logged_in'],$_SESSION['user_name']))
   {

   }
   else
   {
   	header("Location:login.php");
   }

   class Vote {

     private $db_connection = null;

     public $feedback = "";

     public function __construct()
     {
       $this->start();
     }

     public function start()
     {

       $this->createDatabaseConnection();
       $this->storeDB();
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

     private function storeDB()
     {
       $data = $_GET['vote'];
       $id = $_GET['id'];


       $sql = 'Insert Into vote
               Values(:id, :serialized)';
       $query = $this->db_connection->prepare($sql);
       $query->bindValue(':id', $id);
       $query->bindValue(':serialized', $data);
       $query->execute();
       header("location:main.php");

     }
  }

  $application = new Vote;
 ?>
