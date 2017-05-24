<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Orders</title>
        <link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	//Gets username from form
        $user = $_GET['user'];
	//If user is not logged in then they cant see orders
        if ($_GET['user'] == NULL) {
	    //Let user know they need to be logged in and give options to leave page
            echo "Unregistered User: Must be registered user to see orders<br>
            <a href='customerlogin.php'>Customer Login Here</a> <br>
            Don't have account?
            <div><a href='registercustomer.php'><button type='button' value='Register'>Register</a></div><br>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>";
        } else {
	    //Display username and give user options to leave page
            echo "Username: ".$user."<br><a href='customercart.php?user='>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>
	    <a href='customercart.php?user=$user'>
            <input type='submit' name='cart' value='Back to Cart'><br><br></a>";
	    //Contains info for login
	    include("config.php");

	    //Display error if connection to database fails
            if($conn->connect_error){
                die("Connection failed: " . $conn->connection_error);
            }

	    //Display user's orders
            $sql = "SELECT OID, ShipAdd, OrderCost, Status FROM Orders WHERE CID='$user'";
            $result = $conn->query($sql);
	    //Display urser's order info for each order
            if ($result->num_rows  > 0) {
                echo "<table><tr><th>Order ID</th><th>Ship Address</th><th>Order Cost</th><th>Order Status</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>".$row["OID"]."</td><td>".$row["ShipAdd"]."</td><td>".$row["OrderCost"]."</td><td>".$row['Status']."</td></tr>";
                }
                echo "</table><br>";
            } else {
		//Alerts user that they have no orders
                $error = "Your account has no orders placed";
            }

        }

?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
