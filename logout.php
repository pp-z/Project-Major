<?php
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION['olx'])) {session_start();}
unset($_SESSION['olx']);
session_destroy();
header("Location: index.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>logging out....</title>
</head>
<body></body>
</html>