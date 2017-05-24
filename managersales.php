<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Sales </title>
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
	$isManager = False;
        //Checks if user is a manager
        $sqlman = "SELECT isManager FROM Workers WHERE WID='$user'";
        $resultsman = $conn->query($sqlman);
        if ($resultsman->num_rows == 1) {
            $isManager = True;
        }
        //If user is not a manager then they cant look at sales
        if ($_GET['user'] == NULL || !$isManager ) {
	    //Let user know they need to be logged in and give options to leave page
            echo "Must be manager to see sales<br>
            <a href='stafflogin.php'>Staff Login Here</a> <br>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Store'><br><br></a>";
        } else {
	    //Display username and give user options to leave page
            echo "Username: ".$user."<br><a href='stafflogin.php'>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='staffinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Inventory'><br><br></a>";

	    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		if (isset($_POST['year'])){
		    //Check for sales from the last year
		    $sql = "SELECT OID,Status,OrderDate,OrderCost,ShipAdd FROM Orders 
			WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 YEAR)";
		    $result = $conn->query($sql);
    		    //Display sales from the last year
		    if ($result->num_rows > 0) {
			echo "Orders from the last year:<br>";
	                echo "<table><tr><th>Order ID</th><th>Order Cost</th><th>Order Date</th>
			    <th>Order Status</th><th>Shipping Address</th></tr>";
			while($row = $result->fetch_assoc()){
                    	    echo "<tr><td>".$row["OID"]."</td><td>".$row["OrderCost"]."</td><td>"
				.$row["OrderDate"]."</td><td>".$row['Status']."</td><td>"
				.$row['ShipAdd']."</td></tr>";
			}
	                echo "</table><br>";
		    } else {
			$error = "There were no orders in the last year.";
		    }

		    //Check ordered items from the last year
                    $sql = "SELECT Inventory.Name,OrderedItems.OID,OrderedItems.PID,OrderedItems.Quantity 
				FROM OrderedItems JOIN Inventory 
				WHERE Inventory.PID = OrderedItems.PID AND OrderedItems.OID IN
                        	(SELECT OID FROM Orders WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 YEAR))";
                    $result = $conn->query($sql);
                    //Display ordered items from the last year
                    if ($result->num_rows > 0) {
                        echo "Ordered Items from the last year:<br>";
                        echo "<table><tr><th>Order ID</th><th>Product </th><th>Product ID</th>
                            <th>Order Quantity</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["OID"]."</td><td>".$row["Name"]."</td><td>"
                                .$row["PID"]."</td><td>".$row["Quantity"]."</td></tr>";
                        }
                        echo "</table><br>";
                    } else {
                        $error = "There were no ordered items in the last year.";
                    }

		} else if (isset($_POST['month'])){
		    //Display sales from the last month
    		    $sql = "SELECT OID,Status,OrderDate,OrderCost,ShipAdd FROM Orders 
                        WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 MONTH)";
                    $result = $conn->query($sql);
                    //Display sales from the last month
                    if ($result->num_rows > 0) {
                        echo "Orders from the last month:<br>";
                        echo "<table><tr><th>Order ID</th><th>Order Cost</th><th>Order Date</th>
                            <th>Order Status</th><th>Shipping Address</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["OID"]."</td><td>".$row["OrderCost"]."</td><td>"
                                .$row["OrderDate"]."</td><td>".$row['Status']."</td><td>"
                                .$row['ShipAdd']."</td></tr>";
                        }
                        echo "</table><br>";
                    } else {
                        $error = "There were no orders in the last month.";
                    }

                   //Check ordered items from the last month
                    $sql = "SELECT Inventory.Name,OrderedItems.OID,OrderedItems.PID,OrderedItems.Quantity 
                                FROM OrderedItems JOIN Inventory 
                                WHERE Inventory.PID = OrderedItems.PID AND OrderedItems.OID IN
                                (SELECT OID FROM Orders WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 MONTH))";
                    $result = $conn->query($sql);
                    //Display ordered items from the last month
                    if ($result->num_rows > 0) {
                        echo "Ordered Items from the last month:<br>";
                        echo "<table><tr><th>Order ID</th><th>Product </th><th>Product ID</th>
                            <th>Order Quantity</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["OID"]."</td><td>".$row["Name"]."</td><td>"
                                .$row["PID"]."</td><td>".$row["Quantity"]."</td></tr>";
                        }
                        echo "</table><br>";
                    } else {
                        $error = "There were no ordered items in the last month.";
                    }

		}else {
		    //Display sales from the last week
		    $sql = "SELECT OID,Status,OrderDate,OrderCost,ShipAdd FROM Orders 
                        WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 WEEK)";
                    $result = $conn->query($sql);
                    //Display sales from the last week
                    if ($result->num_rows > 0) {
                        echo "Orders from the last week:<br>";
                        echo "<table><tr><th>Order ID</th><th>Order Cost</th><th>Order Date</th>
                            <th>Order Status</th><th>Shipping Address</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["OID"]."</td><td>".$row["OrderCost"]."</td><td>"
                                .$row["OrderDate"]."</td><td>".$row['Status']."</td><td>"
                                .$row['ShipAdd']."</td></tr>";
                        }
                        echo "</table><br>";
                    } else {
                        $error = "There were no orders in the last week.";
                    }

		    //Check ordered items from the last week
                    $sql = "SELECT Inventory.Name,OrderedItems.OID,OrderedItems.PID,OrderedItems.Quantity 
                                FROM OrderedItems JOIN Inventory 
                                WHERE Inventory.PID = OrderedItems.PID AND OrderedItems.OID IN
                                (SELECT OID FROM Orders WHERE OrderDate >= (CURRENT_DATE - INTERVAL 1 WEEK))";
                    $result = $conn->query($sql);
                    //Display ordered items from the last week
                    if ($result->num_rows > 0) {
                        echo "Ordered Items from the last week:<br>";
                        echo "<table><tr><th>Order ID</th><th>Product </th><th>Product ID</th>
                            <th>Order Quantity</th></tr>";
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>".$row["OID"]."</td><td>".$row["Name"]."</td><td>"
                                .$row["PID"]."</td><td>".$row["Quantity"]."</td></tr>";
                        }
                        echo "</table><br>";
                    } else {
                        $error = "There were no ordered items in the last week.";
                    }
		}
	    }
        }

?>
<form action = "managersales.php?user=<?php echo $user; ?>" method = "post">
        <div>
        <input type="submit" name="week" id="week" value="Sales From Last Week">
        </div>
</form>

<form action = "managersales.php?user=<?php echo $user; ?>" method = "post">
        <div>
        <input type="submit" name="month" id="month" value="Sales From Last Month">
        </div>
</form>

<form action = "managersales.php?user=<?php echo $user; ?>" method = "post">
        <div>
        <input type="submit" name="year" id="year" value="Sales From Last Year">
        </div>
</form>

	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; ?></div>
</body>
</html>
