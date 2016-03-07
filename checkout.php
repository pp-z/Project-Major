<?php
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION)) {session_start();}
if (!isset($_SESSION['olx']) || $_SESSION['olx']=='guest') {
	header("Location: index.php");
}
$err='';
if (isset($_POST['prd'])) {
	$conn=mysql_connect("localhost","root","");
	if($conn){
		if(mysql_select_db('olx',$conn)){
			$result=mysql_query("SELECT user_id FROM user WHERE user_name='".$_SESSION['olx']."'",$conn);
			$r=mysql_query("SELECT pcount, price FROM products WHERE id=".$_POST['prd'],$conn);
			$r=mysql_fetch_array($r);
			$row=mysql_fetch_array($result);
			if($_POST['pcount']<=0 || $_POST['pcount']>$r['pcount']){
				$err='Couldnt Buy the Product';
			}
			else{
				if($_SESSION['rupees']>=$r['price']*$_POST['pcount']){
					$qry = "INSERT INTO cart (uid,pid,pcount) VALUES (".$row['user_id'].",".$_POST['prd'].",".$_POST['pcount'].")";
					$res=mysql_query($qry,$conn);
					$qry = "UPDATE products SET pcount=".($r['pcount']-$_POST['pcount'])." WHERE id=".$_POST['prd'];
					$res=mysql_query($qry,$conn);
					$_SESSION['rupees']-=$r['price']*$_POST['pcount'];
					$qry = "UPDATE user SET rupees=".$_SESSION['rupees']." WHERE user_id=".$row['user_id'];
					$res=mysql_query($qry,$conn);
					$err = "Successfully Bought";
				}
				else{
					$err='Couldnt Buy the Product as not enough money';
				}
			}
		}
		else{
			$err='database error';
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Buying....</title>

</head>
<body><div><?php echo $err; ?></div><!-- <br><div><a href="product.php">Go Back</a></div> --><br></body>
</html>