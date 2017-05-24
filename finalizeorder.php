<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Finalize Customer Order</title>
        <link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	//Gets username from form
        $user = $_GET['user'];
	//If user is not logged in then order cant be finalized
        if ($_GET['user'] == NULL) {
	    //Let user know they need to be logged in and give options to leave page
	    $error = "Order could not be finalized because you are not signed in. Please sign in to order.";
            echo "Unregistered User: Must be registered user to have a cart and to buy items<br>
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
            <input type='submit' name='cart' value='Back to Cart'><br><br></a>
	    <a href='customerorder.php?user=$user'>
            <input type='submit' name='order' value='Back to Order Form'><br><br></a>";
	    //Contains info for login
	    include("config.php");

	    //Display error if connection to database fails
            if($conn->connect_error){
                die("Connection failed: " . $conn->connection_error);
            }

	    //Checks to make sure form has all textboxes filled out
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['orderCost'] != NULL && $_POST['shipAdd'] != NULL
		&& $_POST['billAdd'] != NULL && $_POST['ccNum'] != NULL && $_POST['ccName'] != NULL) {
		$orderCost = $_POST['orderCost'];
		$shipAdd = $_POST['shipAdd'];
		$ccnum = $_POST['ccNum'];
		$ccname = $_POST['ccName'];
		$billAdd = $_POST['billAdd'];
		//Attempts to insert order into Orders
		$sql = "INSERT INTO Orders (CID,Status,OrderDate,OrderCost,ShipAdd,CCNum,CCName,BillAdd) 
		VALUES ('$user','Pending',CURDATE(),'$orderCost','$shipAdd','$ccnum','$ccname','$billAdd')";
		$result = $conn->query($sql);
		//If failed alert user
		if ($result->affected_rows < 0) {
		    $error = "Error completing order. Please try again.";
		} else {
		    //Order succeeded so now insert all items from cartItems into OrderedItems
		    //Gets order ID to insert into OrderedItems
		    $oid = "";
		    $sql = "SELECT OID FROM Orders WHERE CID='$user' AND ShipAdd='$shipAdd' AND CCNum='$ccnum' 
		    AND CCName='$ccname' AND BillAdd='$billAdd' AND OrderDate=CURDATE() AND OrderCost='$orderCost'";
		    $resultoid = $conn->query($sql);
		    //If there is no found oid then system error
		    if ($resultoid->num_rows != 1){
			$error = "Error finding order. Please contact the system administrator";
		    } else {
			while($row = $resultoid->fetch_assoc()){
			    $oid = $row['OID'];
			}
			//Insert items from CartItems into OrderedItems
			$sqlinsert = "INSERT INTO OrderedItems (OID,PID,Quantity) 
			    SELECT '$oid',PID,Quantity FROM CartItems WHERE CID='$user'";
			$insertcheck = $conn->query($sqlinsert);
			if ($insertcheck->affected_rows < 0) {
			    $error = "Error transferring items from cart to order. Contact the system administrator.";
			} else {
			    //Since items where successfully transferred to OrderedItems remove from cart
			    $sqldelete = "DELETE FROM CartItems WHERE CID='$user'";
			    $deletecheck = $conn->query($sqldelete);
			    if ($deletecheck->affected_rows < 0) {
				$error = "Error removing items from cart. Please contact the system administrator";
			    } else {
				$error = "Order was successful";
			    }
			}
		    }
		}
            }
	}
?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
