<!DOCTYPE HTML>
<html>
   <head>
      <title>Main Page</title>
      <link rel="stylesheet" type="text/css" href="Assets/CSS/site.css">
      <link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <meta charset="UTF-8">
   </head>
   <body>
      <div id="main">
         <header>
            <nav>
               <a href="loginpage.php"><img src="Assets/Images/DIT_logocol_reverse2013.png"></img></a>
               <span id="spanNav">
                  <h1> DITSU Vote Counter </h1>
               </span>
               <ul>
                 <li><a href="register.php">Register</a></li>
				<li><a class="active" href="loginpage.php">Login</a></li>
               </ul>
            </nav>
         </header>
      </div>
	  
      <div id="content">
         <form method="post" action="register.php" class="w3-container w3-card-4 w3-light-grey w3-text-blue w3-margin">
            <h2 class="w3-center">Candidate Names</h2>
			<div class="w3-row w3-section">
               <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-user"></i></div>
               <div class="w3-rest">
                  <input class="w3-input w3-border" type="username" name="Candidate1" required placeholder="Candidate 1"><span class="error"><span>
               </div>
            </div>

			<div class="w3-row w3-section">
               <div class="w3-col" style="width:50px"><i class="w3-xxlarge fa fa-user"></i></div>
               <div class="w3-rest">
                  <input class="w3-input w3-border" type="username" name="Candidate2" required placeholder="Candidate 2">
               </div>
            </div>
			
            <input type="submit" value="Submit" class="w3-button w3-block w3-section w3-blue w3-ripple w3-padding">
         </form>
      </div>
   </body>
</html>
