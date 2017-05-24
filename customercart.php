<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Customer Cart</title>
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
            <div><a href='registercustomer.php'><button type='button' value='Register'>Register</a></div><br><br>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>";
        } else {
	    //Display username and give user options to leave page
            echo "Username: ".$user."<br><a href='customercart.php?user='>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>
	    <a href='orders.php?user=$user'>
            <input type='submit' name='orders' value='Orders'><br><br></a>";
	    //Contains info for login
	    include("config.php");

	    //Display error if connection to database fails
            if($conn->connect_error){
                die("Connection failed: " . $conn->connection_error);
            }

	    //Removes items from user cart if form was filled out
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['itemCart'] != NULL && $_POST['quantCart'] != NULL) {
		$rmItemName = $_POST['itemCart'];
		$rmItemQuant = $_POST['quantCart'];
		if ($rmItemQuant <= 0) {
		    $error = "Quantity was not a positive number. Please try again.";
		} else {
		//Checks to see if item with at least rmItemQuant quantity is in cart
		$sql = "SELECT CartItems.PID, CartItems.Quantity FROM Inventory JOIN CartItems 
		WHERE Inventory.PID=CartItems.PID AND Inventory.Name='$rmItemName' 
		AND CartItems.CID='$user' AND CartItems.Quantity >= $rmItemQuant";
		$result = $conn->query($sql);

		//If 1 row then rmItemName does exist and can be removed from the cart
		if ($result->num_rows == 1) {
		    while($row = $result->fetch_assoc()) {
		        $itemPID = $row['PID'];
			//What the quantity of item in cart should be after update
			$newItemQuant = $row['Quantity'] - $rmItemQuant;
			//If updated item quantity is 0 then delete item from cart
			if ($newItemQuant == 0) {
			    $sql = "DELETE FROM CartItems WHERE CartItems.PID='$itemPID' AND CID='$user'";
			    $checkDelete = $conn->query($sql);
			    //If -1 is returned alert user there was an error in updating cart
                            if ($checkDelete->affected_rows < 0) {
                                $error = "Item couldn't be removed from cart try again. If error continues contact system administrator";
                            }
			} else {
			    //Update item quantity to $newItemQuant
			    $sql = "UPDATE CartItems SET Quantity = $newItemQuant 
				WHERE PID='$itemPID' AND CID='$user'";
                            $checkUpdate = $conn->query($sql);
                            //If -1 is returned alert user there was an error in updating cart
                            if ($checkUpdate->affected_rows < 0) {
                                $error = "Item couldn't be removed from cart try again. If error continues contact system administrator";
                            }
			}
		    }

		} else if ($result->num_rows == 0) {
		     //No item as described by user is in cart or remove quantity is larger than cart quantity
		    $error = "Item could not be removed from cart. Item either doesn't exist or you tried to remove more of item from cart than exists in the cart.";
		} else {
		    //Alert user to their being an error
		    $error = "There is an error in the database multiple items of same name exist in cart. Please contact the system administrator.";
		}
		}
	    }

	    //Display items in user's cart
            $sql = "SELECT Inventory.Name, CartItems.Quantity, Inventory.Cost, Inventory.PID FROM CartItems JOIN Inventory 
			WHERE CartItems.CID = '$user' AND CartItems.PID = Inventory.PID";
            $result = $conn->query($sql);
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


	        //Textbox form so user can remove items from inventory 
?>
	        <form action = "customercart.php?user=<?php echo $user; ?>" method = "post">
                <div>
                Item to remove from cart:
                <input type="text"  name="itemCart" id="itemCart" value=""><br>
                Quantity of item to remove:
                <input type="text" name="quantCart" id="quantCart" value=""><br>
                <input type="submit" name="cart" value="Submit">
                </div>
	        </form><br><br>

	        <form action = "customerorder.php?user=<?php echo $user; ?>" method = "post">
                <div>
                <input type="submit" name="order" value="Order Items">
                </div>
	        </form>


<?php            } else {
		//Alerts user that their cart is empty
                $error = "There are no items in your cart";
            }

        }

?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
