<?php
include("top.php");
echo '<div class="container row">';
echo '<form method="post" action="' . $_SERVER['SCRIPT_NAME'] . '" name="electionform">';

  $count = 2;

          echo '<div class="card-action">';
          echo '<label for="login_input_username" class="blue-text">Enter Prefrence 1,2,3...</label> ';
          echo '<input id="login_input_username" type="number" name="user_name" min="1" max="2"/> ';
          echo '</div>';

echo '<input class="btn btn-large waves-effects blue col s3 offset-s2" type="submit"  name="login" value="Log in" />';
echo '</form>'

?>
