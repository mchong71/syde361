<html>
<head>

<?php

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
		if(success())
		{
			mysql_query("Update States Set Filled = 0 Where Package_ID = " . $packageID);
			echo ("Congrats you picked up your mother fucking package");
		}
	}
		
}

function checkBox($packageID){

	$box = get_SQLarray("SELECT COUNT(Package_ID) as Count, Filled From States Where Package_ID = '" . $packageID . "'");
	// check to make sure the package exists within the box.
	if ($box['Count'] == 0)
	{
		die("The package" . $packageID . " you are trying to pick up does not exist!");
		return False;
	}
	
	// check to make sure the box the package is in has a package in it
	if ($box['Filled'] == 0)
	{
		die("There doesn't appear to be the package:" . $packageID . " in the box. Please contact BB");
		return False;
	}
		return True;
	
}

// method that returns true if the user successfully opens door picks up package and closes door
function success() {
	return True;
}
function get_SQLarray($query){
	$result = mysql_query($query);
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



