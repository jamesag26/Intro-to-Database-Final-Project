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
	//Contains info for login
        include("config.php");

	//Display error if connection to database fails
        if($conn->connect_error){
            die("Connection failed: " . $conn->connection_error);
        }
	//Gets username from form
        $user = $_GET['user'];
	//If user is not logged in then they have no cart to look at
        if ($_GET['user'] == NULL) {
	    //Let user know they need to be logged in and give options to leave page
            echo "Unknown User: Must be staff to see orders<br>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>";
        } else {
	    //Attempts to update the given order to shipped if possible
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['updateOrder'] != NULL) {
		$oid = $_POST['updateOrder'];
		//Checks to make sure order hasnt been shipped already
		$sqlcheck = "SELECT Status FROM Orders WHERE OID='$oid' AND Status='Pending'";
		$orderStat = $conn->query($sqlcheck);
		if ($orderStat->num_rows == 1) {
		
		//Check if all items in order are available in inventory
		$sql = "SELECT PID, Quantity FROM OrderedItems WHERE OID='$oid'";
		$orderItems = $conn->query($sql);
		if ($orderItems->num_rows > 0) {
		    //Is set to 0 when not all items are ready to ship
		    $shipReady = 1;
		    while($row = $orderItems->fetch_assoc()){
			if ($shipReady == 1){
			    $tempPID = $row['PID'];
			    $orderQuant = $row['Quantity'];
			    $inventoryQuant = 0;
			    $sqlitem = "SELECT Quantity FROM Inventory WHERE PID='$tempPID'";
			    $inventoryCheck = $conn->query($sqlitem);
			    //Checks to see if quantity of item is enough. Only runs once because PID is unique
			    while($itemRow = $inventoryCheck->fetch_assoc()){
				$inventoryQuant = $itemRow['Quantity'];
			    }
			    if ($inventoryQuant < $orderQuant){
				$shipReady = 0;
			    }
			}
		    }
		    //If order is ready to ship then change the status and remove items from Inventory
		    if ($shipReady == 1) {
			$sqlShip = "UPDATE Inventory INNER JOIN OrderedItems 
			    SET Inventory.Quantity=Inventory.Quantity-OrderedItems.Quantity
			    WHERE OrderedItems.OID='$oid' AND OrderedItems.PID=Inventory.PID";
			$updateCheck = $conn->query($sqlShip);
			if ($updateCheck->affected_rows < 0){
			    $error = "Items could not be removed from inventory. Please try again.";
			} else {
			    //Update order since all items can be shipped
			    $sql = "UPDATE Orders SET Status='Shipped' WHERE OID='$oid'";
			    $updateresult = $conn->query($sql);
			}
		    }
		} else {
		    $error = "There are no items in the specified order";
		}
		} else {
		    $error = "Order has already been shipped";
		}
	    }


	    //Display username and give user options to leave page
            echo "Worker's ID: ".$user."<br><a href='stafforders.php?user='>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='staffinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Inventory'><br><br></a>";

	    //Display all  orders
            $sql = "SELECT OID, OrderDate, CID, ShipAdd, OrderCost FROM Orders WHERE Status='Pending'";
            $result = $conn->query($sql);
	    //Display user's order info for each order
            if ($result->num_rows  > 0) {
		echo "Pending Orders:<br>";
                while($row = $result->fetch_assoc()) {
                    echo "<table><tr><th>Order ID</th><th>Order Cost</th><th>Order Date</th><th>Customer ID</th>
		    <th>Ship Address</th></tr>";
                    echo "<tr><td>".$row["OID"]."</td><td>".$row["OrderCost"]."</td><td>".$row["OrderDate"].
		    "</td><td>".$row['CID']."</td><td>".$row['ShipAdd']."</td></tr>";
                    echo "</table><br>";
		    $oid = $row['OID'];
		    $sqlorder = "SELECT PID, Quantity FROM OrderedItems WHERE OID='$oid'";
		    $orderitems = $conn->query($sqlorder);
		    while($items = $orderitems->fetch_assoc()){
                        echo "<table><tr><th>Product ID</th><th>Quantity</th></tr>";
                        echo "<tr><td>".$items["PID"]."</td><td>".$items["Quantity"]."</td></tr>";
                        echo "</table><br>";
		    }
                }

            } else {
		//Alerts user there are no pending orders
                $error2 = "There are no pending orders.";
            }
        }
?>
<form action = "stafforders.php?user=<?php echo $user; ?>" method = "post">
        <div>
        Order ID to Update:
        <input type="text" name="updateOrder" id="updateOrder" value=""><br>
	<input type="submit" name="order" value="Update Order">
	</div>
</form>

	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; echo "<br>"; echo $error2;?></div>
</body>
</html>
