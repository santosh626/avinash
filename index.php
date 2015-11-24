
<html>
<head>		  
 		<link href="css/bootstrap.css" rel="stylesheet" type="text/css"  />

<title>Student Acheivements</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="images/fave-icon.png" />
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
		</script>
   		<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
   		<link href="css/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
		<script src="js/modernizr.custom.28468.js"></script>
		<link rel="stylesheet" type="text/css" href="css/simptip-mini.css" media="screen,projection" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<style >

/*---start-header---*/
.logo {
	float: left;
}
.logo span{
	height: 36px;
	width: 45px;
	display: inline-block;
	background: url(bvblogo.jpg) no-repeat 0px 0px;
	vertical-align: initial;
	margin-right: 7px;
}
.logo a{
	font-family: 'trump_town_proregular';
	color: #3A494C;
	font-size: 3em;
	text-transform: uppercase;
}
.logo {
	float: left;
}
.logo span{
	height: 36px;
	width: 45px;
	display: inline-block;
	background: url(bvblogo.png) no-repeat 0px 0px;
	vertical-align: initial;
	margin-right: 7px;
}
.logo a{
	font-family: 'trump_town_proregular';
	color: #3A494C;
	font-size: 3em;
	text-transform: uppercase;
}
#right{
	float: right;
		width: 55%;

}
#left{
	margin-left: 10px;

	margin-top: 100px;
	float: left;
	width: 40%;
}
#reg{
	margin-top: 20px;
}</style>
	</head>
	<body>
			<!---start-header----->
			<div class="header" id="home">
		<!---start-wrap----->
				<div class="wrap">
				<div class="top-header">
					<div class="logo">
						<a href="index.html"><span> </span>BVBCET</a>
					</div>
					<div class="top-nav">
						<ul>
							<li class="active"><a href="home.html" class="scroll">Home</a></li>
							<li><a href="search.php" class="scroll">Search</a></li>
							<li><a href="logout.php" class="scroll">Logout</a></li>
							<div class="clear"> </div>
						</ul>
					</div>
					<div class="clear"> </div>
				</div>
			<!---End-header----->
			<!----start-content-slider---->


	
<?php 
function login_attempt_count($seconds, $pdo) {
	try {
		// First we delete old attempts from the table
		$del_old = "DELETE FROM attempts WHERE `when` < ?";
		$oldest = strtotime(date("Y-m-d H:i:s")." - ".$seconds." seconds");
		$oldest = date("Y-m-d H:i:s",$oldest);
		$del_data = array($oldest);
		$remove = $pdo->prepare($del_old);
		$remove->execute($del_data);
		
		// Next we insert this attempt into the table
		$insert = "INSERT INTO attempts (`ip`, `when`) VALUES ( ?, ? )";
		$data = array($_SERVER['REMOTE_ADDR'], date("Y-m-d H:i:s"));
		$input = $pdo->prepare($insert);
		$input->execute($data);
		
		// Finally we count the number of recent attempts from this ip address	
		$count = "SELECT count(*) as number FROM attempts where `ip` = ?";
		$num = $pdo->prepare($count);
		$num->execute(array($_SERVER['REMOTE_ADDR']));
		foreach($num as $attempt) {
			$attempts = $attempt['number'];
		}
		return $attempts;
	} catch (PDOEXCEPTION $e) {
		echo "Error: ".$e;
	}
}
//Connects to your Database 
mysql_connect("localhost", "root","") or die(mysql_error()); 
mysql_select_db("intradb") or die(mysql_error());

$dsn = "mysql:host=localhost;dbname=intradb";
$username = "root";
$password = "";
// Above is my test database information. You'll need to use your own. See Dormilich's tutorial on PDO for more information.
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
$pdo = new PDO($dsn, $username, $password, $options);
$max_time_in_seconds = 10;
$max_attempts = 3; 
//Checks if there is a login cookie
if(isset($_COOKIE['ID_your_site'])){ //if there is, it logs you in and directes you to the members page
 	$username = $_COOKIE['ID_your_site']; 
 	$pass = $_COOKIE['Key_your_site'];
 	$arole = 3;
 	$check = mysql_query("SELECT * FROM users WHERE username = '$username'")or die(mysql_error());

 	$isAdmin = mysql_query("SELECT username from users where role='$arole'")or die (mysql_error());
 	if($inadmin = mysql_fetch_array( $isAdmin)){
 		if(!$inadmin){
 			echo 'not admin';
 		}
 		if ($pass != $inadmin['password']){
 			echo "";
 		}else{
 			header("Location: Admin.php");
 		}
 	}else{

 	while($info = mysql_fetch_array( $check )){
 		if ($pass != $info['password']){
 			die('Error').mysql_error();
 		}
 		else{
 			
		}
 	}
 	}
 }

 //if the login form is submitted 
 if (isset($_POST['submit'])) {

	// makes sure they filled it in
 	if(!$_POST['username']){
 		die('You did not fill in a username.');
 	}
 	if(!$_POST['pass']){
 		die('You did not fill in a password.');
 	}

 	// checks it against the database
 	

 	$check = mysql_query("SELECT * FROM users WHERE username = '".$_POST['username']."'")or die(mysql_error());

 //Gives error if user dosen't exist
 $check2 = mysql_num_rows($check);
 if ($check2 == 0){
	die('That user does not exist in our database.<br /><br />If you think this is wrong <a href="login.php">try again</a>.');
}

while($info = mysql_fetch_array( $check )){
	$_POST['pass'] = stripslashes($_POST['pass']);
 	$info['password'] = stripslashes($info['password']);
 	$_POST['pass'] = md5($_POST['pass']);

	//gives error if the password is wrong
 	if ($_POST['pass'] != $info['password']){
		if(login_attempt_count($max_time_in_seconds, $pdo) <= $max_attempts) 
		{
                  die('Incorrect password, please <a href="index.php">try again</a>.');
				  $max_attempts=$max_attempts+1;
    // Now show the login form
        }
		else 
		{

                 echo "I'm sorry, you've made too many attempts to log in too quickly.<br>";
    // Do not show the login form, since it may be a bot trying to log in.
         }

 		
 	}
	
	else{ // if login is ok then we add a cookie 
		$_POST['username'] = stripslashes($_POST['username']); 
		$hour = time() + 3600; 
		setcookie(ID_your_site, $_POST['username'], $hour); 
		setcookie(Key_your_site, $_POST['pass'], $hour);	 
 		if($info['role']==1){
 			header("Location: home.html");
 		}
		//then redirect them to the members area 
		else if ($info['role']==3){
			header("Location: Admin.php"); 
		}
	}
}
}
else{
// if they are not logged in 
?>
<div>
	<div id="left" >
 <form class="form-horizontal" role="form" action="<?php echo $_SERVER['PHP_SELF']?>" method="post"> 

	<div class="form-group">
      <label class="control-label col-sm-2" for="username">Username:</label>
      <div class="col-sm-6">
        <input type="text" class="form-control" name="username" required maxlength="40">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="username">Password:</label>
      <div class="col-sm-6">
         <input type="password" class="form-control" name="pass" maxlength="50"> 
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-3">
 			<input  type="submit" class="btn btn-lg btn-primary" name="submit" value="Login"> 
      </div>
    </div>
</div>
<div id="right">
<div class="slider8">
  <div class="slide"><img src="images/bvbclite.jpg"></div>
  <div class="slide"><img src="images/bvbclite.jpg"></div>
  
</div>
</div>
	<br style="clear:both;"/>
</div>

 </form> 
<div>
	If you have not registered you can  <a class="btn btn-lg btn-large" href="add.php">REGISTER </a>
</div>
 <?php 
 }
 ?> 
</body>
</html>
