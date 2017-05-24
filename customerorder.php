<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Customer Order</title>
        <link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	//Gets username from form
        $user = $_GET['user'];
	//If user is not logged in then they have no cart to look at
        if ($_GET['user'] == NULL) {
	    //Let user know they need to be logged in and give options to leave page
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
	    <a href='orders.php?user=$user'>
            <input type='submit' name='orders' value='Orders'><br><br></a>";
	    //Contains info for login
	    include("config.php");

	    //Display error if connection to database fails
            if($conn->connect_error){
                die("Connection failed: " . $conn->connection_error);
            }

	    //Display items in user's cart
            $sql = "SELECT Inventory.Name, CartItems.Quantity, Inventory.Cost, Inventory.PID FROM CartItems JOIN Inventory 
			WHERE CartItems.CID = '$user' AND CartItems.PID = Inventory.PID";
            $result = $conn->query($sql);
	    $orderCost = 0;
	    //Display item, quantity and cost of item for each item in cart
            if ($result->num_rows  > 0) {
                echo "<table><tr><th>Item ID</th><th>Quantity</th><th>Current Cost</th></tr>";
                while($row = $result->fetch_assoc()) {
		    //Check if item is on sale and if so use that value for itemcost
		    $itempid = $row['PID'];
		    $sqlsalecheck = "SELECT SaleCost FROM Promotions WHERE PID='$itempid'";
		    $salecheck = $conn->query($sqlsalecheck);
		    //If item is on sale use that item price if not use default
		    if ($salecheck->num_rows == 1) {
			while($saleRow = $salecheck->fetch_assoc()){
                            echo "<tr><td>".$row["Name"]."</td><td>".$row["Quantity"]."</td><td>".
				$saleRow["SaleCost"]."</td></tr>";
		            $orderCost = $orderCost + $saleRow['SaleCost'] * $row['Quantity'];
			}
		    } else {
                        echo "<tr><td>".$row["Name"]."</td><td>".$row["Quantity"]."</td><td>"
			    .$row["Cost"]."</td></tr>";
		        $orderCost = $orderCost + $row['Cost'] * $row['Quantity'];
		    }
                }
                echo "</table><br>";

	        //Textbox form so user can finish their order 
?>
	        <form action = "finalizeorder.php?user=<?php echo $user; ?>" method = "post">
                <div>
		Total Cost:
		<input type="text" name="orderCost" id="orderCost" value="<?php echo $orderCost ?>" readonly><br>
		Shipping Address:
                <input type="text"  name="shipAdd" id="shipAdd" value=""><br>
                Billing Address:
                <input type="text" name="billAdd" id="billAdd" value=""><br>
                Credit Card Number:
                <input type="text" name="ccNum" id="ccNum" value=""><br>
		Credit Card Name:
                <input type="text" name="ccName" id="ccName" value=""><br>
                <input type="submit" name="order" value="Complete Order">
                </div>
	        </form>

<?php            } else {
		//Alerts user that their cart is empty
                $error = "There are no items in your cart to order";
            }

        }

?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
