<?php
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION)) {session_start();}
if (!isset($_SESSION['olx']) || $_SESSION['olx']=='guest' ||  $_SESSION['category']!='A') {
	header("Location: index.php");
}
$err='';
if (isset($_POST['submitted']) && $_POST['submitted']=="1") {
	if (isset($_POST['pname']) && isset($_POST['pprice']) && isset($_POST['pcount'])) {
		$pname=addslashes($_POST['pname']);
		$pprice=addslashes($_POST['pprice']);
		$pcount=addslashes($_POST['pcount']);
		$pdesc=addslashes($_POST['pdesc']);
		$target_dir = "products/";
		$target_file = $target_dir . basename($_FILES["pimg"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$target_file = $target_dir . $pname . "." . $imageFileType;
		$check = getimagesize($_FILES["pimg"]["tmp_name"]);
		$conn=mysql_connect('localhost','root','');
			if($conn){
				if(mysql_select_db('olx',$conn)){
					$result=mysql_query("SELECT name,price,postedby,pcount FROM products WHERE name='$pname' AND price=$pprice AND postedby='".$_SESSION['olx']."'",$conn);
					if (!mysql_num_rows($result)) {
						if ($check && file_exists($target_file)){
							$check = false;
						}
						else if ($check && !move_uploaded_file($_FILES["pimg"]["tmp_name"], $target_file)){
							$check = false;
						}
						if($check === false)
							$result=mysql_query("INSERT INTO products (name, price, postedby, pcount, description) VALUES ('$pname','$pprice','".$_SESSION['olx']."',$pcount, '$pdesc')",$conn);
						else
							$result=mysql_query("INSERT INTO products (name, price, postedby, pcount, description, img) VALUES ('$pname','$pprice','".$_SESSION['olx']."',$pcount, '$pdesc', '$target_file')",$conn);
						if (!$result) {
							$err='Couldnt post';
						}
						else{
							header("Location: product.php");	
						}
					}
					else{
						$row=mysql_fetch_array($result);
						$result=mysql_query("UPDATE products SET pcount=".$row['pcount']+$pcount." WHERE name='$pname' AND price=$pprice AND postedby='".$_SESSION['olx']."'",$conn);
						if (!$result) {
							$err='Couldnt post';
						}
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
?>

<!DOCTYPE html>
<html>
<head>
	<title>Post</title>
	<style type="text/css">
	form div{
		padding: 5px;
	}
	</style>
</head>
<body>
<div style='float:right;'>
		<div style="float:left;padding:15px;">Hello <?php echo $_SESSION['olx'];?></div>
		<div style="float:left;padding:15px;"><a href="logout.php">Signout</a></div>
	</div>
<div style="float:left;">
		<div style="float:left;padding:15px;"><a href="product.php">HOME</a></div>
		<div style="float:left;padding:15px;"><a href="orders.php">CART</a></div>
	</div>
<br><br>
<h1 style="text-align:center;">Post Item to Sell</h1>
<div style="margin:auto;text-align:center;">
	<div style="display:none;"><?php echo $err; ?></div>
	<form action="post.php" method="post" enctype="multipart/form-data">
		<div style="display:none;"><input type="text" name="submitted" value="1"></div>
		<div>Product name : <input type="text" name="pname" placeholder="Product Name"></div>
		<div>Product price : <input type="number" name="pprice" placeholder="Price"></div>
		<div>Product count : <input type="number" name="pcount" placeholder="Count"></div>
		<div>Product image : <input type="file" name="pimg"></div>
		<div>Product Description : <textarea name="pdesc"></textarea></div>
		<input type="submit" name="submit" value="Post">
	</form>
</div>
</body>
</html>