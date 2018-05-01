<?php


session_start();

if(isset($_SESSION['admin_Toggle']))
{

}
else
{
	header("Location:login.php");
}


class admin {


  public function __construct()
  {
    $this->display();
  }

private function display()
{

  include('top.php');
  echo '<br>';
  echo '<br>';
  echo '<div class="container grey lighten-2 z-depth-5">';
  echo '<form method="get" action="votecount.php" name="electionform">';
  echo '<label for="login_input_username" class="blue-text">Enter Which Election You\'d like to Count</label> ';
  echo '<input id="election_id" type="number" min="1"  name="id" /> ';
  echo '<input class="btn btn-large waves-effects blue col s3 offset-s2" type="submit" />';
  echo '</form>';
  echo '</div>';
  include('bottom.php');
}


}

$application = new admin;

?>
