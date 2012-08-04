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
	
		mysql_free_result($result);
		
	} else { //if user inputs specific column and box
		$_COL = $sizeORcol;
		$_BOX = $specBox;
	}
	
	//----------------
	$_COMPARTMENT = $_COL . $_BOX;
	
	//unlock the box
	$_SERIAL->writeMsg("U", $_COL, $_BOX);
	
	//after we call to unlock the box, start "processing"
	packageProcessing();	
}

function packageProcessing() {
	global $_BOX, $_COL, $_COMPARTMENT, $_SERIAL;
	
	$_SERIAL->writeMsg("L", $_COL, $_BOX);
	$lockData = $_SERIAL->readMsg();

	if($lockData == 0) {
		//-If door fails to open attempt to open again.
		//-Call packageProcessing
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
			echo "Door successfully closed!";
			doorClosed();
		} else {
			//Should never come here
			die("DOOR CLOSE FAIL");
		}
	} else {
		//-Put error handling here to send user back to main page.
		//-Should never come here under normal circumstances 
		die("Large Error. Start Again.");
	}
}

function doorClosed() {

	global $_BOX, $_COL, $_COMPARTMENT, $_PACKINTSIZE, $_SERIAL;
	$_SERIAL->writeMsg("S", $_COL, $_BOX);
	$sensorData = $_SERIAL->readMsg();
	//$sensorData = 1;
	echo $sensorData;
	$pack_ID = -1;
	
	if($sensorData == 0) {
		//unfilled - FAIL
		die("YOU HAVE FAILED US");
		
	} elseif($sensorData == 1) {
		//filled - SUCCESS!
		$pack_ID = ($_BOX*2)+(7*$_COL);
           
        //putting the new parcel into the bb. Sets the package ID and filled status
		$query = sprintf("UPDATE States Set Filled = 1, Package_ID=" .$pack_ID. " Where Column_ID=" .$_COL." && Box_ID=" .$_BOX);
		$result = mysql_query($query);
	
		if (!$result) {
    		$message  = 'Invalid query: ' . mysql_error() . "\n";
	   		$message .= 'Whole query: ' . $query;
    		die($message);
		} else {
			echo "</br></br>Package has been successfully dropped off with ID: ".$pack_ID;
		}
		
		mysql_free_result($result);
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