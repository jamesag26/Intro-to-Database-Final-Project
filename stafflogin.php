<!DOCTYPE html>
<html>
<head>
<style>
#list {
	text-align:center;
	padding:5px;
}
#list2 {
	text-align:center;
	padding:5px;
}
</style>
	<title> Staff Log In </title>
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
            $sql = "SELECT WID, isManager FROM Workers WHERE WID = '$myusername' AND Password = '$mypassword'";
            $result = $conn->query($sql);
            //If username and password matched then result must have one row
            if ($result->num_rows  == 1) {
		//Sends staff to staff inventory page with username
                header("location: staffinventory.php?user=".$myusername);
            } else {
                $error = "Incorrect username and password combination";
            }
        }
?>

<!-- Form to allow user to login -->
<form action="" method="post">
	<div id="list">
	User name:
	<input type="text"  name="username" id="username" value=""><br>
	Password:
	<input type="password" name="password" id="password" value=""><br>
	<a href="loginsuccess.php"><input type="submit" value="Submit"><br></a>
	</div>
	<div id = "list2">
	Click <a href="customerlogin.php">here</a> to transport to customer log in page<br>
</form>
<!-- Displays error to user if any occur -->
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;">
	<?php echo $error; ?></div>
</body>
</html>

