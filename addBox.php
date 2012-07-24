<html>
<head>

<?php

include 'messaging.php';


//$sizeArr = array(0 => 1, 1 => 2, 2 => 3, 3 => 1, 4 => 2, 5 => 3);

//Get COM Port
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());
	
function addBox() {
	$serial = new messaging();
	//sends message to backend telling them to expect a new locker
	$serial->writeMsg("N");
	$numOfBox = $serial->readMsg();

	if($numOfBox != -1)
	{
		$result= mysql_query("Select Column_ID from States");
		$rows = mysql_num_rows($result);
		mysql_free_result($rows);
		if ($rows == 0)
			$new_ID = 1;
		else 
		{
			$column_ID = get_SQLarray("Select MAX(Column_ID) as Column_ID from States");
			$new_ID = $column_ID['Column_ID'] + 1;
		}
		$r = -1;
		// sends next available column id for addressing
		do {
			$serial->writeMsg("A", $new_ID);
			$r = $serial->readMsg();
		} while ($r != 0);
		// address was successfully assigned
		$count = 0;
		for($i = 1; $i <= $numOfBox; $i++)
		{
				// get data on each individual box. XXXXX For future could ask for limit and sensor data
				$serial->writeMsg("T", $column_ID . $i);
				$size = $serial->readMsg();
				mysql_query("Insert into States values (0," . $new_ID . "," . $i . ",0,0," . $size . ")"); 
				$count++;
		}
		echo "You have successfully added " . $count . " boxes!";
	}
}

// Function keeps assigning an address until confirmation is received
/*function assignAdd($new_ID)
{
	$serial->writeMsg("A");
	$r = $serial->readMsg();
	if($r== 0)
		return true;
	else 
		return false;
}*/

function get_SQLarray($query) 
{
	$result = mysql_query($query);
	$array = mysql_fetch_array($result);
	mysql_free_result($array);
	return $array;
}
?>

</head>
<body class="page_bg">

<!-- addBox could only take in an array where the first element is the number of
		boxes the next elements are the corresponding sizes -->
<?php addBox();?>

</body>
</html>
