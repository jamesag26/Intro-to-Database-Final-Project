<!DOCTYPE html>
<html>
<head>
<style>
#list {
	text-align:center;
	padding:5px;
}
</style>
	<title>Staff Inventory</title>
	<link rel="stylesheet" type="text/css" href="mstone.css">
</head>

<body>
<?php
	$user = $_GET['user'];
        if ($_GET['user'] == NULL) {
            echo "No staff logged in<br>";
        } else {
            echo "Worker ID: ".$user."<br><br>";
	    echo "<a href='stafforders.php?user=$user'>
            <input type='submit' name='orders' value='Manage Orders'><br><br></a>";

	include("config.php");

	if($conn->connect_error){
	    die("Connection failed: " . $conn->connection_error);
	}

	//Items on sale
	$sql = "SELECT Inventory.Name, Promotions.SaleCost, Inventory.Quantity FROM Inventory JOIN Promotions
                WHERE Promotions.PID = Inventory.PID";
        $result = $conn->query($sql);
        if ($result->num_rows  > 0) {
            echo "Items On Sale:";
            echo "<table><tr><th>Item Name</th><th>Cost</th><th>Quantity</th></tr>";
            while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["Name"]."</td><td>".$row["SaleCost"]."</td><td>".$row["Quantity"]."</td></tr>";
            }
            echo "</table><br>";
        }

	//Regular inventory not on sale
        $sql = "SELECT Inventory.Name, Inventory.Cost, Inventory.Quantity FROM Inventory JOIN Promotions
		WHERE Promotions.PID != Inventory.PID";
        $result = $conn->query($sql);
        if ($result->num_rows  > 0) {
	    echo "Items Not on Sale:";
            echo "<table><tr><th>Item Name</th><th>Cost</th><th>Quantity</th></tr>";
            while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["Name"]."</td><td>".$row["Cost"]."</td><td>".$row["Quantity"]."</td></tr>";
            }
            echo "</table><br>";
        } else {
            $error = "No items exist in the inventory";
        }

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['item'] != NULL && $_POST['quant'] != NULL) {
	    $item = $_POST['item'];
	    $quant = $_POST['quant'];
	    $sql = "UPDATE Inventory SET Quantity='$quant' WHERE Name='$item'";
	    $result = $conn->query($sql);
	    if ($result !== TRUE) {
                $error = "Item quantity could not be updated";
	    }
	}

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['newName'] != NULL && $_POST['newQuant'] != NULL && $_POST['newCost'] != NULL && $_POST['newType'] != NULL) {
            $item = $_POST['newName'];
            $quant = $_POST['newQuant'];
	    $cost = $_POST['newCost'];
	    $type = $_POST['newType'];
            $sql = "INSERT INTO Inventory (Name,Cost,Quantity,Type) VALUES ('$item','$cost','$quant','$type')";
            $result = $conn->query($sql);
            if ($result !== TRUE) {
                $error = "Item could not be added to inventory";
            } 
        }

?>
<form action = "staffinventory.php?user=<?php echo $user;?>" method = "post">
        <div>
        Item Name to Update:
        <input type="text"  name="item" id = "item" value=""><br>
	New Updated Quantity:
        <input type="text"  name="quant" id = "quant" value=""><br>
        <a href="staffinventory.php?user=<?php echo $user;?>"><input type="submit" name="update" value="Submit"><br></a>
        </div><br><br>

        <div>
        New Product:
        <input type="text"  name="newName" id="newName" value=""><br>
        Quantity of Product:
        <input type="text" name="newQuant" id="newQuant" value=""><br>
        Cost of Product:
        <input type="text" name="newCost" id="newCost" value=""><br>
        Product Type('Games' or 'Books'):
        <input type="text" name="newType" id="newType" value=""><br>
        <a href="customerinventory.php?user=<?php echo $user;?>"><input type="submit" name="cart" value="Submit"><br></a>
        </div><br><br>
</form>

<?php
	//Checks if user has permission to see these menus
	$sql = "SELECT isManager FROM Workers WHERE WID='$user'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
	    //User has permission to see promotions and sales options
	    echo "Manager Options<br>
            <a href='managerpromotions.php?user=$user'>
            <input type='submit' name='promotions' value='Manage Promotions'><br><br></a>
	    <a href='managersales.php?user=$user'>
            <input type='submit' name='promotions' value='View Sales'><br><br></a>";
	}
	}
?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
