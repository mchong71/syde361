<html>
<head>

<?php

include 'messaging.php';

//Get COM Port
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());


function pickup($packageID) {
	if (checkBox($packageID))
	{
		if(success($packageID))
		{
			mysql_query("Update States Set Filled = 0, Package_ID = 0 Where Package_ID = '" . mysql_real_escape_string($packageID) . "'");
			echo ("Thank you for using Buffer Box");
		}
	}	
}

function checkBox($packageID){

	$box = get_SQLarray("SELECT COUNT(Package_ID) as Count, Filled From States Where Package_ID = '" . mysql_real_escape_string($packageID) . "'");
	// check to make sure the package exists within the box.
	if ($box['Count'] == 0)
	{
		die("ERROR: The package: " . $packageID . " you are trying to pick up does not exist!<br>
				Check to make sure you entered the ID correctly.");
		return False;
	}
	
	// check to make sure the box the package is in has a package in it
	if ($box['Filled'] == 0)
	{
		die("ERROR: There doesn't appear to be the package: " . $packageID . " in the box. Please contact BB");
		return False;
	}
		return True;	
}

// method that returns true if box is unfilled and door is closed
function success($packageID) {
	$box = get_SQLarray("SELECT Column_ID, Box_ID FROM States WHERE Package_ID = '" . mysql_real_escape_string($packageID) . "'");
	$col = $box['Column_ID'];
	$box = $box['Box_ID'];
	
	$serial = new messaging();
	$serial->writeMsg("U", $col, $box);

	$lockResult = 1;
	//$serial->writeMsg("S", $col, $box);
	$sensorResult = 1;//$serial->readMsg();
	
	while($lockResult != 0) {
			$serial->writeMsg("L", $col, $box);
			$lockResult = $serial->readMsg();
	}
	
	if ($lockResult == 0 && $sensorResult == 1) //stats messages check
		return true;
	else if($lockResult == 0 && $sensorResult == 0)
		die("Seems as though you didn't pick up your package. Please repeat the process!");
	
	
}
function get_SQLarray($query){
	$result = mysql_query($query);
	if(!$result)
	{echo $result;}
	$array = mysql_fetch_array($result);
	return $array;
}
?>

</head>
<body class="page_bg">
<?php pickup($_POST["packageID"]);?>

</body>
</html>
