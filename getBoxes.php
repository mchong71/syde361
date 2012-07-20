<html>
<head>

<?php
include 'php_serial.php';

function readMsg($fd) {

	/*$response = "";
	$data = serial->readPort();
	echo "curD: " . $data;
	while($data != chr(10))
	{
		$response = $response . $data;
		$data = serial->readPort();
	}*/

	return "C24";//$response;
}

function get_Size($size) {
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("$dbhost", "$dbuser", "$dbpass") or die (mysql_error());
	mysql_select_db("$dbname") or die(mysql_error());
	$com_string = '/dev/cu.usbmodem621';//'COM1';
	
	// Formulate Query
	//$query = sprintf("SELECT Package_ID, Column_ID, Box_ID, Filled, Open, Size FROM States");

	// Perform Query
	//$result = mysql_query($query);
	
	//Redo This!
	$packIntSize = -1;
	switch($size) {
		case 'S':
			$packIntSize = 0;
			break;
		
		case 'M':
			$packIntSize = 1;
			break;
		
		case 'L':
			$packIntSize = 2;
			break;
		
		default:
			echo ("ERRORRRRRRR");
			break;
	}
	
	$query = sprintf("SELECT * From States WHERE Size='" .$packIntSize. "' && Filled='0' LIMIT 1");
	$result = mysql_query($query);
	
	if (!$result) {
    	$message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
    	die($message);
	} 
	
	$resultArr = mysql_fetch_array($result);
	$selectedCol = $resultArr['Column_ID'];
	$selectedBox = $resultArr['Box_ID'];
	$selectedComp = $selectedCol . $selectedBox;

	echo ($selectedCol . " | " . $selectedBox);

	// set up serial connection
	$serial = new phpSerial();
	$serial->deviceSet($com_string);
	$serial->confBaudRate(9600); //Baud rate: 9600 
    $serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
    $serial->confCharacterLength(8); //Character length 
    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
    
    //$serial->confFlowControl("none");
	//Device does not support flow control of any kind, 
	//so set it to none. 
	
    //Now we "open" the serial port so we can write to it 
    $serial->deviceOpen(); 
	$serial->sendMessage("U" . $selectedComp .chr(10));
	
	echo 'Message has been Sent!';
	
	$door_opened = 0;

	//They loop this 20 times to allow for any errors. Look into a better way to do this
	$i=0;
	while($i < 20)
	{
	//Wait for response from arduino's. Query arduino's. Wait for Matt
    $data = readMsg();

    if($data)
    {
        $data = trim($data);

        if($data == "O" . $selectedComp)
        {
        	echo "YSY";
            $door_opened = 1;
            
            //set dummy package ID. We'll fix this later
            /*$pack_ID = (i*3)+($selectedBox*2);
            
            //putting the new parcel into the bb. Sets the package ID and filled status
			$query = sprintf("UPDATE States Set Filled = 1, Package_ID='" .$pack_ID. "' Where Size='" .$packIntSize. "' && Filled='0' && Column_ID='" .$selectedCol."' Box_ID='" .$selectedBox."'");
			$result = mysql_query($query);
	
			if (!$result) {
    			$message  = 'Invalid query: ' . mysql_error() . "\n";
	    		$message .= 'Whole query: ' . $query;
    			die($message);
			}

			//test to ensure the is an active internet connection
            /*$conn = @fsockopen("www.google.com", 80, $errno, $errstr, 30);

            if($conn)
            {
            	//This seems to be writing to an offsite BB db
            }*/

            //echo '<META HTTP-EQUIV="Refresh" Content="0; URL=http://localhost/DoorOpened.html?compartment=' . $compartment . '">';
            break;
        }
        elseif($data == "C" . $selectedComp)
        {
        	//If door is not open, attempt to send message again
        	$serial->sendMessage("U" . $selectedComp .chr(10));
        }
        else { echo "failed to open";}
    }
    
    //query status?

    usleep(100000);
    $i++;
	}

//The reaction if the door failed to open
//include method here

	$serial->deviceClose(); 
	mysql_free_result($result);
}		
?>
</head>
<body>
	<?php
		get_Size($_POST["pSize"]);
	?>
</body>
</html>