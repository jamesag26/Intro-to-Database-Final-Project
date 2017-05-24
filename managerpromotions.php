<!DOCTYPE html>
<html>
<head>
<style>
#list {
        text-align:center;
        padding:5px;
}
</style>
        <title>Promotions</title>
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
	//If user is not logged in then they have no cart to look at
        if ($_GET['user'] == NULL || !$isManager ) {
	    //Let user know they need to be logged in and give options to leave page
            echo "Unknown User: Must be manager to see promotions<br>
	    <a href='customerinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Inventory'><br><br></a>";
        } else {
	    //Attempts to add given promotion
	    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['promotionid'] != NULL && $_POST['newcost'] != NULL) {
		$salecost = $_POST['newcost'];
		$pid = $_POST['promotionid'];
		//Check if item already has a promotion
		$sql = "SELECT PID FROM Promotions WHERE PID='$pid'";
		$promotedItem = $conn->query($sql);
		if ($promotedItem->num_rows > 0) {
		    //Item is already being promoted. Need to update promotion
		    $sql = "UPDATE Promotions SET SaleCost='$salecost' WHERE PID='$pid'";
		    $updateCheck = $conn->query($sql);
		    if ($updateCheck->affected_rows < 0){
			$error = "Item promotion couldn't be updated.";
		    }
		} else {
		    //Insert new promotion
		    $sql = "INSERT INTO Promotions (PID,SaleCost) VALUES ('$pid','$salecost')";
		    $insertCheck = $conn->query($sql);
		    if (!$insertCheck){
			$error = "Item promotion failed. Please try again.";
		    }
		}
	    }

	    //Attempts to remove given promotion
            if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['removeid'] != NULL){
                $pid = $_POST['removeid'];
                //Check if item is in promotions
                $sql = "SELECT PID FROM Promotions WHERE PID='$pid'";
                $promotedItem = $conn->query($sql);
                if ($promotedItem->num_rows > 0) {
                    //Item is being promoted. Remove promotion
                    $sql = "DELETE FROM Promotions WHERE PID='$pid'";
                    $deleteCheck = $conn->query($sql);
                    if ($deleteCheck->affected_rows < 0){
                        $error = "Item promotion couldn't be updated.";
                    }
                }
            }



	    //Display username and give user options to leave page
            echo "Worker's ID: ".$user."<br><a href='stafforders.php?user='>
            <input type='submit' name='logout' value='Log Out'><br><br></a>
	    <a href='staffinventory.php?user=$user'>
            <input type='submit' name='inventory' value='Back to Inventory'><br><br></a>";

	    //Display all promotions
            $sql = "SELECT PID, SaleCost FROM Promotions";
            $result = $conn->query($sql);
	    //Display user's order info for each order
            if ($result->num_rows  > 0) {
		echo "Current Promotions:<br>";
                while($row = $result->fetch_assoc()) {
                    echo "<table><tr><th>Product ID</th><th>New Cost</th><th>Order Date</th></tr>";
                    echo "<tr><td>".$row["PID"]."</td><td>".$row["SaleCost"]."</td></tr>";
                    echo "</table><br>";
		    $oid = $row['OID'];
                }
            } else {
		//Alerts user there are no promotions
                $error2 = "There are no promotions.";
            }
        
?>
<form action = "managerpromotions.php?user=<?php echo $user; ?>" method = "post">
        <div>
        Add Promotions:<br>
	Item ID
        <input type="text" name="promotionid" id="promotionid" value=""><br>
	New Cost
        <input type="text" name="newcost" id="newcost" value=""><br>
	<input type="submit" name="order" value="Add Promotion">
	</div>
</form>

<form action = "managerpromotions.php?user=<?php echo $user; ?>" method = "post">
        <div>
        Remove Promotions:<br>
        Item ID
        <input type="text" name="removeid" id="removeid" value=""><br>
        <input type="submit" name="removepromotion" value="Remove Promotion">
        </div>
</form>

<?php } ?>
	<div style = "font-size:25px; color:#cc0000; margin-top:10px; text-align:center;"><?php echo $error; echo "<br>"; echo $error2;?></div>
</body>
</html>
