<!DOCTYPE html>
<html>
<head>
<style>
#list {
	text-align:center;
	padding:5px;
}
</style>
	<title>Log in Client</title>
	<link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	include("config.php");

	if($conn->connect_error){
	    die("Connection failed: " . $conn->connection_error);
	}

	if($_SERVER["REQUEST_METHOD"] == "POST") {
	    //username and password sent from form

	    $myusername = $_POST['username'];
	    $mypassword = $_POST['password'];
	    $sql = "SELECT CID FROM Customers WHERE CID = '$myusername' AND Password = '$mypassword'";
	    $result = $conn->query($sql);
	    //If username and password matched then result must have one row
	    if ($result->num_rows  == 1) {
		//Send user to inventory with username
		header("location: customerinventory.php?user=".$myusername);
	    } else {
		$error = "Incorrect username and password combination";
	    }
	}
?>
<!-- Form for user to input login information -->
<form action = "" method = "post">
	<div id="list">
	Username:
	<input type="text"  name="username" id = "username" value=""><br>
	Password:
	<input type="password"  name="password" id = "password" value=""><br>
	<a href="customerinventory.php"><input type="submit" value="Submit"><br></a>
	Don't have account?
	<a href="registercustomer.php"><button type="button" value="Register">Register</a>
	</div>
</form>
<!-- Will display error to user if any occur -->
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
