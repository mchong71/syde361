<html>
<head>

<?php
include 'messaging.php';

$_BOX = -1;
$_COL = -1;
$_COMPARTMENT = -1;
$_PACKINTSIZE = -1;

function get_Size($size) {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE;
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("$dbhost", "$dbuser", "$dbpass") or die (mysql_error());
	mysql_select_db("$dbname") or die(mysql_error());
	
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
	$_BOX = $resultArr['Column_ID'];
	$_COL = $resultArr['Box_ID'];
	$_COMPARTMENT = $_BOX . $_COL;

	echo ($_COMPARTMENT);
	
	//free the result
	mysql_free_result($result);

	// set up serial connection
	$serialp = new messaging;
	$serialp->writeMsg("U", $_COMPARTMENT);
	
	echo 'Message has been Sent!';
	//$serialp->writeMsg("L", $_COMPARTMENT);
	//$limStat = $serialp->readMsg();
	//packageProcessing();	
}

function doorClosed() {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE;
	echo "indoors";
	$serialp = new messaging;
	$serialp->writeMsg("S", $_COMPARTMENT);
	$sensorData = 1;
	
	if($sensorData == 0) {
		//unfilled - FAIL
		//die();
		
	} elseif($sensorData == 1) {
		//filled - SUCCESS!
		$pack_ID = (i*3)+($_BOX*2);
           
        //putting the new parcel into the bb. Sets the package ID and filled status
		$query = sprintf("UPDATE States Set Filled = 1, Package_ID=" .$pack_ID. " Where Size=" .$_PACKINTSIZE. " && Filled=0 && Column_ID=" .$_COL." && Box_ID=" .$_BOX);
		$result = mysql_query($query);
	
		if (!$result) {
    		$message  = 'Invalid query: ' . mysql_error() . "\n";
	   		$message .= 'Whole query: ' . $query;
    		die($message);
		}
		
		echo "Input Package!!!!";
	}
}

function packageProcessing() {
	global $_BOX, $_COL, $_COMPARTMENT;
	$serialp = new messaging;
	$serialp->writeMsg("L", $_COMPARTMENT);
	$lockData = 1;//$serialp->readMsg();//get message data from serial
	
	if($lockData == 0) {
		$serialp->writeMsg("U", $_COMPARTMENT);
		packageProcessing();
		
	} elseif($lockData == 1) { //success in opening
		//spam backend until door is closed
		$limitData = 1;
		
		while($limitData != 0) {
			$serialp->writeMsg("L", $_COMPARTMENT);
			echo "checked for limit";
			$limitData = 0;
		}
		
		//ensure door is closed
		if($limitData == 0) {
			echo "door is closed";
			doorClosed();
		} else {
			die();
		}
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