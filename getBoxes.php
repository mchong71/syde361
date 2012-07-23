<html>
<head>

<?php
include 'messaging.php';

$_BOX = -1;
$_COL = -1;
$_COMPARTMENT = -1;
$_PACKINTSIZE = -1;
$_SERIAL = new messaging();

$dbhost = "localhost";
$dbname = "kiosk_map";
$dbuser = "root";
$dbpass = "root";
$link = mysql_connect("$dbhost", "$dbuser", "$dbpass") or die (mysql_error());
mysql_select_db("$dbname") or die(mysql_error());

function get_Size($size) {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE, $_SERIAL;
	
	//Redo This!
	switch($size) {
		case 'S':
			$_PACKINTSIZE = 0;
			break;
		
		case 'M':
			$_PACKINTSIZE = 1;
			break;
		
		case 'L':
			$_PACKINTSIZE = 2;
			break;
		
		default:
			echo ("ERRORRRRRRR");
			break;
	}
	
	$query = sprintf("SELECT * From States WHERE Size='" .$_PACKINTSIZE. "' && Filled='0' LIMIT 1");
	$result = mysql_query($query);
	
	if (!$result) {
    	$message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
    	die($message);
	} 
	
	$resultArr = mysql_fetch_array($result);
	$_COL = $resultArr['Column_ID'];
	$_BOX = $resultArr['Box_ID'];
	$_COMPARTMENT = $_BOX . $_COL;

	echo ($_COMPARTMENT);

	// set up serial connection
	//if(!$_SERIAL->writeMsg("U", $_COMPARTMENT)) { echo "failed";}
	//else { echo "writing worked";}
	$_SERIAL->writeMsg("U", $_COMPARTMENT);
	echo 'Message has been Sent!';
	packageProcessing();	
	
	//free the result
	mysql_free_result($result);
}

function doorClosed() {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE, $_SERIAL;
	$_SERIAL->writeMsg("S", $_COMPARTMENT);
	$sensorData = $_SERIAL->readMsg();
	$pack_ID = -1;
	
	if($sensorData == 0) {
		//unfilled - FAIL
		die("YOU HAVE FAILED US");
		
	} elseif($sensorData == 1) {
		//filled - SUCCESS!
		$pack_ID = 1113;//($_BOX*2)+7;
           
        //putting the new parcel into the bb. Sets the package ID and filled status
		$query = sprintf("UPDATE States Set Filled = 1, Package_ID=" .$pack_ID. " Where Size=" .$_PACKINTSIZE. " && Column_ID=" .$_COL." && Box_ID=" .$_BOX);
		//echo "UPDATE States Set Filled = 1, Package_ID=" .$pack_ID. " Where Size=" .$_PACKINTSIZE. " && Column_ID=" .$_COL." && Box_ID=" .$_BOX;
		$result = mysql_query($query);
	
		if (!$result) {
    		$message  = 'Invalid query: ' . mysql_error() . "\n";
	   		$message .= 'Whole query: ' . $query;
    		die($message);
		} else {
			echo "</br>Package has been successfully dropped";
		}
		
		mysql_free_result($result);
	}
}

function packageProcessing() {
	global $_BOX, $_COL, $_COMPARTMENT, $_SERIAL;
	
	$_SERIAL->writeMsg("L", $_COMPARTMENT);
	$lockData = $_SERIAL->readMsg();
	echo $lockData;
	if($lockData == 0) {
		$_SERIAL->writeMsg("U", $_COMPARTMENT);
		packageProcessing();
		echo "fail";
	} elseif($lockData == 1) { //success in opening
		//spam backend until door is closed
		$limitData = 0;
		/*while($limitData != 0) {
			$_SERIAL->writeMsg("L", $_COMPARTMENT);
			echo "checked for limit";
			$limitData = $serialp->readMsg();
		}*/
		
		//ensure door is closed
		if($limitData == 0) {
			//doorClosed();
		} else {
			die("DOOR CLOSE FAIL");
		}
	} else {
		die("FATAL ERROR!");
	}
}
		
?>
</head>
<body>
	<?php
		get_Size($_POST["pSize"]);
	?>
</body>
</html>