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

function get_Size($sizeORcol, $specBox = 0) {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE, $_SERIAL;
	
	//HACK: This needs to be re-done. Not very good solution
	//-----------
	
	//If user only enter the "size" they want
	if($specBox==0) {
	//Redo This!
	switch($sizeORcol) {
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
	
	if(mysql_num_rows($result) == 0)
		die("There are no boxes of that type available!");
	else {
		$_COL = $resultArr['Column_ID'];
		$_BOX = $resultArr['Box_ID'];
	}
	$resultArr = mysql_fetch_array($result);
	
	
	} else { //if user inputs specific column and box
		$_COL = $sizeORcol;
		$_BOX = $specBox;
	}
	
	//----------------
	
	
	$_COMPARTMENT = $_COL . $_BOX;
	
	//echo $_COMPARTMENT."</br></br>";

	// Attempt to unlock the box
	$_SERIAL->writeMsg("U", $_COL, $_BOX);
	
	packageProcessing();	
	
	//echo '<META HTTP-EQUIV="Refresh" Content="0; URL=http://localhost/ErrorPage.html">';
	//free the result
	mysql_free_result($result);
}

/*function doorClosed() {
	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE, $_SERIAL;
	$_SERIAL->writeMsg("S", $_COL, $_BOX);
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
			echo "</br></br>Package has been successfully dropped";
		}
		
		mysql_free_result($result);
	}
}*/

function packageProcessing() {
	global $_BOX, $_COL, $_COMPARTMENT, $_SERIAL;
	
	$_SERIAL->writeMsg("L", $_COL, $_BOX);
	$lockData = $_SERIAL->readMsg();
	//echo "lockdata: ".$lockData;
	if($lockData == 0) {
		$_SERIAL->writeMsg("U", $_COL, $_BOX);
		//packageProcessing();
		die("fail");
		
	} elseif($lockData == 1) { //success in opening
		//spam backend until door is closed
		while($lockData != 0) {
			$_SERIAL->writeMsg("L", $_COL, $_BOX);
			$lockData = $_SERIAL->readMsg();
		}
		
		//ensure door is closed
		if($lockData == 0) {
			echo "</br>door is successfully closed: ".$lockData;
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
<body style="margin:0 auto; text-align=center;padding:50px;color:#fff;font:12px Arial;" background="images/bgimage.png" >
	<div style="border:1px solid #fff; padding:10px;">
		<?php
			if($_POST["pSize"]=="") {
				get_Size($_POST["column"], $_POST["box"]);
			} else {
				get_Size($_POST["pSize"]);
			}
		?>
	</div>
</body>
</html>