<html>
<head>
    <title> Register Page</title>

</head>
<body>
<?php
    include("config.php");

    //Attempts to connect to database
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
	$myusername = $_POST['username'];
	$mypassword = $_POST['password'];
	//Check if username already exists
	$sql = "SELECT CID FROM Customers WHERE CID = '$myusername'";
	$result = $conn->query($sql);
	//Alerts user if usename already exists
	if ($result->num_rows > 0) {
	    $error = "Username already exists pick a new one or sign in if you already have an account";
	} else {
	    //Insert new user login info into database
	    $sql = "INSERT INTO Customers (CID, Password) VALUES ('$myusername', '$mypassword')";
	    $result = $conn->query($sql);
	    header("location: customerinventory.php?user=".$myusername);
	}
    }
?>
<form action="" method = "post">
    Account Name:<br>
    <input type="text" name="username">
    <br>
    Password:<br>
    <input type="text" name="password"><br>
    <input type="submit" value="Submit">
</form>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;">
	<?php echo $error; ?>
</body>

</html>
