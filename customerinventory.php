<!DOCTYPE html>
<html>
<head>
<style>
#list {
	text-align:center;
	padding:5px;
}
</style>
	<title>Customer Shopping</title>
	<link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	//Gets user ID from the form
	$user = $_GET['user'];
	//If user ID is null then they cant add to cart cart
        if ($_GET['user'] == NULL) {
            echo "Unregistered User: Must be registered user to add items to cart and to buy items<br>
            <a href='customerlogin.php'>Customer Login Here</a> <br>
	    Don't have account?
            <div><a href='registercustomer.php'><button type='button' value='Register'>Register</a></div><br><br>";
	//Lets user know their ID and gives them option to return to inventory or to logout
        } else {
            echo "Username: ".$user."<br><a href='customerinventory.php?user='>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='customercart.php?user=$user'>
	    <input type='submit' name='cart' value='Cart'><br><br></a>
	    <a href='orders.php?user=$user'>
            <input type='submit' name='orders' value='Orders'><br><br></a>";
        }

?>
<!-- Form used for searching -->
<form action = "customerinventory.php?user=<?php echo $user;?>" method = "post">
        <div id="list">
        Item to Search:
        <input type="text"  name="search" id = "search" value=""><br>
        <a href="customerinventory.php?user=<?php echo $user;?>"><input type="submit" name="srch" value="Submit"><br></a>
        </div>
<!-- Text boxes for user to add items to cart -->
        <div id="list">
        Item to add to cart:
        <input type="text"  name="itemCart" id="itemCart" value=""><br>
        Quantity of item:
        <input type="text" name="quantCart" id="quantCart" value=""><br>
        <a href="customerinventory.php?user=<?php echo $user;?>"><input type="submit" name="cart" value="Submit"><br></a>
        </div>
</form>

<?php
	include("config.php");

	//Atempts to connect to database and displays error if it can't
	if($conn->connect_error){
	    die("Connection failed: " . $conn->connection_error);
	}

	//Default load database items in inventory unless specific items were searched
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['search'] != NULL) {
	    $itemsearch = $_POST['search'];
	    //Query database to find name, cost and quantity of searched item
	    $sql = "SELECT Name, Cost, Quantity, PID FROM Inventory WHERE Name = '$itemsearch'";
	    $result = $conn->query($sql);
	    //Display items searched. As of now only one item can be found since it looks for complete strings
	    //And the database inventory has unique names for products since the same identical products
	    //Would have the same price,quantity, etc.
	    if ($result->num_rows  > 0) {
		echo "<b>Items in Stock</b>";
		echo "<table><tr><th>Item Name</th><th>Cost</th><th>Quantity</th></tr>";
		while($row = $result->fetch_assoc()) {
		    $itempid = $row['PID'];
		    //Check if item is on sale
		    $sqlsale = "SELECT SaleCost FROM Promotions WHERE PID='$itempid'";
		    $saleresult = $conn->query($sqlsale);
		    if ($saleresult->num_rows == 1){
			while($saleRow = $saleresult->fetch_assoc()){
			    $sale = $saleRow['SaleCost'];
		    	    echo "<tr><td>".$row["Name"]."</td><td>".$saleRow["SaleCost"]."</td><td>".$row["Quantity"]."</td></tr>";
			    echo "</table><br>";
			    echo "<b>The Item is on sale</b><br><br>";
			}
		    } else {
		        echo "<tr><td>".$row["Name"]."</td><td>".$row["Cost"]."</td><td>".$row["Quantity"]."</td></tr>";
		    }
		}
		echo "</table><br>";
	    } else {
		//If query does no return anything then display error info
		$error = "The item you searched for does not exist";
	    }
	} else {
	    //Default displays entire inventory list of items
	    //Items on sale
            $sql = "SELECT Inventory.Name, Promotions.SaleCost, Inventory.Quantity FROM Inventory JOIN Promotions
                WHERE Promotions.PID = Inventory.PID";
            $result = $conn->query($sql);
            if ($result->num_rows  > 0) {
                echo "<b>Items On Sale:</b>";
                echo "<table><tr><th>Item Name</th><th>Sale Cost</th><th>Quantity</th></tr>";
                while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["Name"]."</td><td>".$row["SaleCost"]."</td><td>"
		    .$row["Quantity"]."</td></tr>";
                }
                echo "</table><br>";
            }

            $sql = "SELECT Name, Cost, Quantity FROM Inventory";
	    $sql = "SELECT Inventory.Name, Inventory.Cost, Inventory.Quantity FROM Inventory JOIN Promotions
                WHERE Promotions.PID != Inventory.PID";
            $result = $conn->query($sql);
            if ($result->num_rows  > 0) {
		echo "<b>Other Items in Stock</b>";
                echo "<table><tr><th>Item Name</th><th>Cost</th><th>Quantity</th></tr>";
                while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["Name"]."</td><td>".$row["Cost"]."</td><td>".$row["Quantity"]."</td></tr>";
                }
                echo "</table><br>";
            } else {
		//Displays if no items exist in inventory
                $error = "No items exist in the inventory";
            }
	}

	//If user attempted to add items to inventory check if item exists
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['itemCart'] != NULL && $_POST['quantCart'] != NULL && $user != NULL) {
	    $item = $_POST['itemCart'];
	    $quant = $_POST['quantCart'];
	    //Checks to see if item exists and if so then if quantity described is within inventory quantity limit
	    $sql = "SELECT PID, Quantity FROM Inventory WHERE Name='$item' and Quantity >= $quant";
	    $result = $conn->query($sql);
	    //If 1 item is returned then item exists with acceptable quantity amount
	    if ($result->num_rows == 1) {
                while($row = $result->fetch_assoc()) {
		    //Checks to see if user has this item already in their cart
		    $pid = $row['PID'];
		    $totalQuant = $row['Quantity'];
		    $sql = "SELECT Quantity FROM CartItems WHERE PID=$pid AND CID='$user'";
		    $cartResult = $conn->query($sql);
		    //If item already exists in cart then attempt to add quantity to existing cart quantity
		    if ($cartResult->num_rows == 1) {
			while($cartRow = $cartResult->fetch_assoc()){
			    $cartQuant = $cartRow['Quantity'];
			    //Total quantity user is attempting to have
			    $updateQuant = $cartQuant + $quant;
			    //If updated quantity doesnt exist inventory quantity update user cart
			    if ($totalQuant >= $updateQuant) {
			        $sql = "UPDATE CartItems SET Quantity=$updateQuant WHERE CID = '$user' AND PID=$pid";
			        $checkUpdate = $conn->query($sql);
				//If -1 is returned alert user there was an error in updating cart
			        if ($checkUpdate->affected_rows < 0) {
			            $error = "Cart couldn't be updated try again. If error continues contact system administrator";
			        }
			    } else {
				//Alert user item is in cart and updating quantity would exceed inventory total
				$error = "Item already in cart. Updating cart item quantity would exceed item stock quantity";
			    }
			}
		    } else if ($cartResult->num_rows == 0) {
			//Item is not in cart
			//Attempt to add item to cart
			$sql = "INSERT INTO CartItems (CID,PID,Quantity) VALUES ('$user','$pid','$quant')";
			$checkInsert = $conn->query($sql);
			//Reports error to user if insertion fails
			if ($checkInsert->affected_rows < 0) {
			    $error = "Item couldn't be added to cart try again. If error continues contact system administrator";
			}
		    } else {
			//Error in database let user know that it is system error not user error
			$error = "There is an error with database. Multiple Items in cart with same ID contact system administrator.";
		    }
		}
	    } else if ($result->num_rows == 0) {
		//Displays error since item query didnt return any results
	        $error = "Item couldn't be added to cart. Either forms where filed incorrectly or quantity exceeds inventory quantity";
	    } else {
		//Error where multiple items exist in database with same name
		$error = "There is an error in database with item please contact system administrator.";
	    }
	}
?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
