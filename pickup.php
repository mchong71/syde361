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
			mysql_query("Update States Set Filled = 0 Where Package_ID = '" . mysql_real_escape_string($packageID) . "'");
			echo ("Congrats you picked up your mother fucking package");
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
	$compartment = $box['Column_ID'].$box['Box_ID'];
	
	$serial = new messaging;
	$serial->writeMsg("U", $compartment);
	$serial->writeMsg("L", $compartment);
	$limitResult = $serial->readMsg();
	$serial->writeMsg("S", $compartment);
	$sensorResult = $serial->readMsg();
	
	if ($limitResult == 0 && $sensorResult == 0) //stats messages check)
		return True;
	else if($limitResult == 0 && $sensorResult == 1)
		die();
	return true;
		
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
matt chong is left to wonder....
<?php pickup($_POST["packageID"]);?>




</body>
</html>



