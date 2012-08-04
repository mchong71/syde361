<html>
<head>

<?php

include 'messaging.php';

	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());
	
function addBox() {
	$serial = new messaging();
	$colDetails = $serial->readMsg();
	//echo var_dump($colDetails);
	
	$numCols = $colDetails[1];
	$currId = $colDetails[0];
	echo $numCols . "/".$colDetails."/";
	if($numCols > 0)
	{
		$result= mysql_query("Select Column_ID from States");
		$rows = mysql_num_rows($result);
		$newID = 0;
		
		if ($rows == 0) 
		{
			$newID = 0;
		}
		else 
		{
			$column_ID = get_SQLarray("Select MAX(Column_ID) as Column_ID from States");
			$newID = $column_ID['Column_ID'] + 1;
		}
		
		mysql_free_result($rows);
		
		$r = -1;
		// sends next available column id for addressing
		while($r != 1) {
			$serial->writeMsg(chr(4), $currId, $newID);
			$r = $serial->readMsg();
		}

		// address was successfully assigned
		$count = 0;
		for($i = 1; $i <= $numCols; $i++)
		{
				// get data on each individual box. XXXXX For future could ask for limit and sensor data
				$serial->writeMsg("T", $newID, $i);
				$size = $serial->readMsg();
				mysql_query("Insert into States values (0," . $newID . "," . $i . ",0,0," . $size . ")"); 
				$count++;
		}
		echo "You have successfully added " . $count . " boxes!";
	} else {
		echo "failed";
	}
	
}

function get_SQLarray($query) 
{
	$result = mysql_query($query);
	$array = mysql_fetch_array($result);
	mysql_free_result($array);
	return $array;
}
?>

</head>
<!-- addBox could only take in an array where the first element is the number of
		boxes the next elements are the corresponding sizes -->

<body style="margin:0 auto; text-align=center;padding:50px;color:#fff;font:12px Arial;" background="images/bgimage.png" >
	<div style="border:1px solid #fff; padding:10px;">
		<?php addBox();?>
	</div>
</body>
</html>
