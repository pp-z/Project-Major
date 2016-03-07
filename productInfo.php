<?php
	error_reporting(E_ERROR | E_PARSE);
	if (!isset($_SESSION)) {session_start();}
	if(!isset($_SESSION['olx'])){
		$_SESSION['olx']='guest';
		$_SESSION['rupees']=0;
	}
	if (!(isset($_GET) && isset($_GET['id']))) {
		header("Location: product.php");
	}
	$err='';
	$conn=mysql_connect('localhost','root','');
	if($conn){
		if(mysql_select_db('olx',$conn)){
			$result=mysql_query("SELECT * FROM products WHERE id='".$_GET['id']."'",$conn);
			if (mysql_num_rows($result)<=0) {
				$err='No such product available';
			}
			else{
				$row=mysql_fetch_array($result);
			}
		}
		else{
			$err='database error';
		}
	}
	else{
		$err='connection error';
	}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>ProductInfo</title>
		<link href="css/products.css" rel='stylesheet' type='text/css' />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="images/fav-icon.png" />
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
		</script>
		<script src="http://code.jquery.com/jquery-latest.min.js"  type="text/javascript"></script>
		<script src="js/alertify.js"  type="text/javascript"></script>
		<!----webfonts---->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/alertify.core.css" />
		<link rel="stylesheet" href="css/alertify.default.css" />
		<!----//webfonts---->
		<!---start-click-drop-down-menu----->
		<script src="js/jquery.min.js"></script>
        <!----start-dropdown--->
         <script type="text/javascript">
			var $ = jQuery.noConflict();
				$(function() {
					$('#activator').click(function(){
						$('#box').animate({'top':'0px'},500);
					});
					$('#boxclose').click(function(){
					$('#box').animate({'top':'-700px'},500);
					});
				});
				$(document).ready(function(){
				//Hide (Collapse) the toggle containers on load
				$(".toggle_container").hide(); 
				//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
				$(".trigger").click(function(){
					$(this).toggleClass("active").next().slideToggle("slow");
						return false; //Prevent the browser jump to the link anchor
				});
									
			});
		</script>
        <!----//End-dropdown--->
	</head>
	<body>
		<!---start-wrap---->
			<!---start-header---->
			<div class="header">
				<div class="wrap">
				<div class="logo">
					
				</div>
				<div class="nav-icon">
					 <a href="#" class="right_bt" id="activator"><span> </span> </a>
				</div>
				 <div class="box" id="box">
					 <div class="box_content">        					                         
						<div class="box_content_center">
						 	<div class="form_content">
								<div class="menu_box_list">
									<ul>
										<li><a href="index.php"><span>home</span></a></li>
										<li><a href="recommended.php"><span>Recommended</span></a></li>
										
										<!-- <li><a href="#"><span>Works</span></a></li>
										<li><a href="#"><span>Clients</span></a></li>
										<li><a href="#"><span>Blog</span></a></li> -->
										<?php
										if (!isset($_SESSION)) {session_start();}
										if($_SESSION['category']=='A'){
											echo '<li><a href="post.php"><span>Post</span></a></li>';
										}
										if ($_SESSION['olx']!='guest') {
											echo '<li><a href="orders.php"><span>My Orders</span></a></li>';
											echo '<li><a href="#"><span>Balance:Rs.'.$_SESSION['rupees'].'</span></a></li>';
											echo '<li><a href="logout.php"><span>Logout</span></a></li>';
										}
										?>
										<div class="clear"> </div>
									</ul>
								</div>
								<a class="boxclose" id="boxclose"> <span> </span></a>
							</div>                                  
						</div> 	
					</div> 
				</div>       	  
				<!-- <div class="top-searchbar">
					<form>
						<input type="text" /><input type="submit" value="" />
					</form>
				</div> -->
				<div class="userinfo">
					<div class="user">
						<ul>
							<li><a href="<?php echo($_SESSION['olx']=="guest"?"index.php":"product.php");?>"><img src="images/user-pic.png" title="user-name" /><span><?php echo $_SESSION['olx'];?></span></a></li>
						</ul>
					</div>
				</div>
				<div class="clear"> </div>
			</div>
		</div>
		<!---//End-header---->
		<!---start-content---->
		<div class="content">
			<div class="wrap">
			<div class="single-page">
			<div><?php echo $err;?></div>
			<?php if(mysql_num_rows($result)){ ?>
							<div class="single-page-artical">
								<div class="artical-content">
									<img src="<?php echo $row['img'];?>" title="banner1">
									<h3><a href="#"><?php echo $row['name'];?></a></h3>
									<p><b>Price</b> : Rs.<?php echo $row['price'];?></p>
									<p class="para1"><?php echo $row['pcount'];?> items are Remaining</p>
									<p class="para2"><?php echo $row['description'];?></p> 
									<p class="para2">
										<?php if (isset($_SESSION['olx']) && $_SESSION['olx']!='guest') {
											echo "<div style='padding: 10px;border: 1px solid;width: 290px;'>Buy: <form id='checkout' action='checkout.php' method='post'><input type='hidden' name='prd' value='".$row['id']."'>Count : <input type='number' name='pcount' placeholder='Count'>&nbsp;&nbsp;&nbsp;<input type='submit' name='submit' value='Buy'></form></div>";
										}?>
									</p> 
								    </div>
								   
							<?php }?>
						</div>
						 </div>
		</div>
		<!---start-footer-->
		<div class="footer">
			
		</div>
		<!---//End-footer-->
		<!---//End-wrap-->
	</body>
	<script>
		    var form = $('#checkout');
		    $(form).submit(function(event) {
			    // Stop the browser from submitting the form.
			    event.preventDefault();
			    var formData = $(form).serialize();
			    $.ajax({
				    type: 'POST',
				    url: $(form).attr('action'),
				    data: formData
				})
				.done(function(response) {
			   		alertify.alert(response, function(){
						location.reload();
					});
				})
			});
	</script>
</html>

