<?php
error_reporting(E_ERROR | E_PARSE);
$err='';
if (!isset($_SESSION)) {session_start();}
if (isset($_SESSION['olx']) && $_SESSION['olx']!='guest') {
	header("Location: product.php");
}
if(isset($_POST['submitted'])){
	if(isset($_POST['uname']) && isset($_POST['pass'])){
		$uname=addslashes($_POST['uname']);
		$pass=addslashes($_POST['pass']);
		$pass=md5($pass);
		$conn=mysql_connect('localhost','root','');
		if($conn){
			if(mysql_select_db('olx',$conn)){
				$result=mysql_query("SELECT user_name FROM user WHERE user_name='$uname'",$conn);
				if (!mysql_num_rows($result)) {
					if(mysql_query("INSERT INTO user (user_name,password,category,rupees) VALUES('$uname','$pass','U','100000.00')",$conn)){
						if(!isset($_SESSION)){session_start();}
						$uname=stripslashes($uname);
						$_SESSION['olx']=$uname;
						$_SESSION['catg']='U';
						$_SESSION['rupees']=100000;
						header("Location: product.php");
					}
					else{
						$err='couldnt register';
					}
				}
				else{
					$err='Name already registered';
				}
			}
			else{
				$err='database error';
			}
		}
		else{
			$err='connection error';
		}
	}
	else{
		$err='input is illegal';
	}
}



?>

<!DOCTYPE html>
<html>
<head>
<title>OLX</title>

</head>
<body>

<h1>Sign Up here</h1>

<form action="signup.php" method="POST">
<div><?php echo($err); ?></div>
<div style="display:none;"><input type="text" name="submitted" value='1'></div>
<div>Username : <input type="text" name="uname" placeholder="Username"></div><br>
<div>Password : <input type="password" name="pass" placeholder="Password"></div><br>
<div>gender : <input type="radio" name="sex" value="male">Male  <input type="radio" name="sex" value="female">Female<br><br>

<input type="submit" name="submit" value="Signup">

</form>

</body>

</html>
