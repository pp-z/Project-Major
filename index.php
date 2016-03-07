<?php
	error_reporting(E_ERROR | E_PARSE);
	if (!isset($_SESSION['olx'])) {session_start();}
	if(isset($_SESSION['olx']) && $_SESSION['olx']!="guest"){
		header("Location: product.php");
	}
	$result='';
	$err='';
	$err1='';
	if(isset($_POST['submitted']) && $_POST['submitted']=="1"){
		if(isset($_POST['uname']) && isset($_POST['pass'])){
			$uname=addslashes($_POST['uname']);
			$pass=addslashes($_POST['pass']);
			$pass=md5($pass);
			$conn=mysql_connect('localhost','root','');
			if($conn){
				if(mysql_select_db('olx',$conn)){
					$result=mysql_query("SELECT user_name,rupees,salary,gender,age,category FROM user WHERE user_name='$uname' AND password='$pass'",$conn);
					if (mysql_num_rows($result)>0 && $row = mysql_fetch_assoc($result)) {
						if(!isset($_SESSION)){session_start();}
						$uname=stripslashes($uname);
						$_SESSION['olx']=$uname;
						$_SESSION['rupees']=floatval($row['rupees']);
						$_SESSION['age']=$row['age'];
						$_SESSION['gender']=$row['gender'];
						$_SESSION['salary']=$row['salary'];
						$_SESSION['category']=$row['category'];
						header("Location: product.php");
					}
					else{
						$err='Username or Password is wrong';
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
	}
	if(isset($_POST['signup_submitted'])){
		if(isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['fullname']) &&
		isset($_POST['email']) && isset($_POST['passwordsignup_confirm']) && 
		isset($_POST['job']) && isset($_POST['age']) && isset($_POST['gender']) 
		&& isset($_POST['salary']) && isset($_POST['phonenumber'])){
			$uname=addslashes($_POST['uname']);
			$fullname=addslashes($_POST['fullname']);
			$email=addslashes($_POST['email']);
			$passwordsignup_confirm=addslashes($_POST['passwordsignup_confirm']);
			$pass=addslashes($_POST['pass']);
			$age=addslashes($_POST['age']);
			$job=addslashes($_POST['job']);
			$salary=addslashes($_POST['salary']);
			$gender=addslashes($_POST['gender']);
			$phonenumber=addslashes($_POST['phonenumber']);
			if ($pass != $passwordsignup_confirm) {
				$err1 = "Passwords dont match";
			}
			else{
				$pass=md5($pass);
				$conn=mysql_connect('localhost','root','');
				if($conn){
					if(mysql_select_db('olx',$conn)){
						$result=mysql_query("SELECT user_name FROM user WHERE user_name='$uname'",$conn);
						if (!mysql_num_rows($result)) {
							if(mysql_query("INSERT INTO user (user_name,password,category,rupees,fullname,email,job,age,salary,gender,phonenumber) VALUES('$uname','$pass','U','1000000.00','$fullname','$email','$job','$age','$salary','$gender','$phonenumber')",$conn)){
								if(!isset($_SESSION)){session_start();}
								$uname=stripslashes($uname);
								$_SESSION['olx']=$uname;
								$_SESSION['catg']='U';
								$_SESSION['rupees']=1000000;
								$_SESSION['salary']=$salary;
								$_SESSION['age']=$age;
								$_SESSION['gender']=$gender;
								header("Location: product.php");
							}
							else{
								$err1='couldnt register';
							}
						}
						else{
							$err1='Name already registered';
						}
					}
					else{
						$err1='database error';
					}
				}
				else{
					$err1='connection error';
				}
			}
		}
		else{
			$err1='input is illegal';
		}
	}

?>

<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="UTF-8" />
        <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
        <title>E COMMERCE WEBSITE </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Login and Registration Form with HTML5 and CSS3" />
        <meta name="keywords" content="html5, css3, form, switch, animation, :target, pseudo-class" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    </head>
    <body>
        <div class="container">
            <!-- Codrops top bar -->
            <div class="codrops-top">
                
                <span class="right">
                    
                </span>
                <div class="clr"></div>
            </div><!--/ Codrops top bar -->
            <header>
                <h1>WELCOME TO THE STORE <span></span></h1>
				<nav class="codrops-demos">
					
					
				</nav>
            </header>
            <section>				
                <div id="container_demo" >
                    <!-- hidden anchor to stop jump http://www.css3create.com/Astuce-Empecher-le-scroll-avec-l-utilisation-de-target#wrap4  -->
                    <a class="hiddenanchor" id="toregister"></a>
                    <a class="hiddenanchor" id="tologin"></a>
                    <div id="wrapper">
                        <div id="login" class="animate form">
                            <form  action="index.php" autocomplete="on" method="POST"> 
                                <h1>Log in</h1>
                                <input type="hidden" name="submitted" value="1">
                                <p> 
                                    <label for="username" class="uname" data-icon="u" > Your email or username </label>
                                    <input id="username" name="uname" required="required" type="text" placeholder="myusername or mymail@mail.com"/>
                                </p>
                                <p> 
                                    <label for="password" class="youpasswd" data-icon="p"> Your password </label>
                                    <input id="password" name="pass" required="required" type="password" placeholder="eg. X8df!90EO" /> 
                                </p>
                                <!--<p class="keeplogin"> 
									<input type="checkbox" name="loginkeeping" id="loginkeeping" value="loginkeeping" /> 
									<label for="loginkeeping">Keep me logged in</label>
								</p>-->
                                <p class="login button"> 
                                    
									<input type="submit"  value="Login" /> 
								
								</p>
                                
	                            <p class="error-p"> 
	                                
									<?php echo $err;?>
								
								</p>
								
								
								<p class="change_link">
									Not a member yet ?
									<a href="#toregister" class="to_register">Join us</a>
								</p>
                            </form>
                        </div>

                        <div id="register" class="animate form">
                            <form  action="index.php#toregister" autocomplete="on" method="POST"> 
                                <h1> Sign up </h1>
                                <input type="hidden" name="signup_submitted" value='1'>
                                 <p> 
                                    <label for="fullname" class="fullname" data-icon="u">full name</label>
                                    <input id="fullname" name="fullname" required="required" type="text" placeholder="sachin tendulkar" />
                                </p>
								
								
								
								<p> 
                                    <label for="usernamesignup" class="uname" data-icon="u">Your username</label>
                                    <input id="usernamesignup" name="uname" required="required" type="text" placeholder="mysuperusername690" />
                                </p>
                                <p> 
                                    <label for="emailsignup" class="youmail" data-icon="e" > Your email</label>
                                    <input id="emailsignup" name="email" required="required" type="email" placeholder="mysupermail@mail.com"/> 
                                </p>
                                <p> 
                                    <label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
                                    <input id="passwordsignup" name="pass" required="required" type="password" placeholder="eg. X8df!90EO"/>
                                </p>
                                <p> 
                                    <label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
                                    <input id="passwordsignup_confirm" name="passwordsignup_confirm" required="required" type="password" placeholder="eg. X8df!90EO"/>
                                </p>
                                
								
								<p> 
                                    <label for="job" class="job" data-icon="u">job </label>
                                    <input id="job" name="job" placeholder="eg...." required/>
								
                                </p>
								<p>
									<label for="gender" class="gender" data-icon"u">Gender </label>
									<select name="gender">
										<option value="male">Male </option>
										<option value="female">Female </option>
									</select>
									
								<p> 
                                    <label for="age" class="age" data-icon="u">AGE</label>
                                    <input id="age" name="age" required="required" type="AGE" placeholder="eg. 25"/>
                                </p>
										
								<p> 
                                    <label for="salary" class="salary" data-icon="u">Salary ($)</label>
                                    <input id="salary" name="salary" required="required" type="salary" placeholder="eg. 25000"/>
                                </p>
								
								<p> 
                                    <label for="phonenumber" class="phonenumber" data-icon="u">phone number</label>
                                    <input id="phonenumber" name="phonenumber" required="required" type="phonenumber" placeholder="eg. +919995467892 "/>
                                </p>
								
								
								
								
								
								
								<p class="signin button"> 
									<input type="submit" value="Sign up"/> 
								</p>
	                            <p class="error-p"> 
	                                
									<?php echo $err1;?>
								
								</p>
                                <p class="change_link">  
									Already a member ?
									<a href="#tologin" class="to_register"> Go and log in </a>
								</p>
                            </form>
                        </div>
						
                    </div>
                </div>  
            </section>
        </div>
    </body>
</html>