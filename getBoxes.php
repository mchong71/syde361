<html>
<head>

<?php
/*function bb_read($fd) {

	$response = "";
	$data = dio_read($fd, 1);

	while($data != chr(10))
	{
		$response = $response . $data;
		$data = dio_read($fd, 1);
	}

	return $response;
}*/

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
	
	// Check result
	// This shows the actual query sent to MySQL, and the error. Useful for debugging.
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
	
	//TESTING PHP SERIAL SCRIPT
	//require("php_serial.class.php");
	include 'php_serial.php';

	$serial = new phpSerial();
	$serial->deviceSet($com_string);
		
	$serial->confBaudRate(9600); //Baud rate: 9600 
	echo "yy";
    $serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
    echo "yys"; 
    $serial->confCharacterLength(8); //Character length 
    echo "yya";
    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
    echo "yyw";
    //$serial->confFlowControl("none");
    echo "yye";
	//Device does not support flow control of any kind, 
	//so set it to none. 
	
    //Now we "open" the serial port so we can write to it 
    $serial->deviceOpen(); 
	
	////
	
	echo 'yes1';
	/*exec('mode ' . $com_string . ': baud=9600 data=8 stop=1 parity=n xon=on');
	echo 'yes2';
	$fd = dio_open($com_string, O_RDWR);
	echo 'yes3';
	if(!$fd)
	{
    	die("Error when open " . $com_string);
	}
	
	echo 'yes4';
	
	// Writing to the serial line
	dio_write ($fd , "unlock:" . $selectedComp);
	dio_write ($fd , chr(13).chr(10));
*/
	
	$serial->sendMessage("U" . $selectedComp);
	
	echo 'wrote to line';
/*	
$door_opened = 0;

$i=0;
while($i < 20)
{
    $data = bb_read($fd);

    if($data)
    {
        $data = trim($data);

        if($data == "opened:" . $selectedComp)
        {
            $door_opened = 1;

            /*mysql_connect("localhost", "root", "") or die(mysql_error());
            mysql_select_db("kiosk_map") or die(mysql_error());

            //Remove the parcel from the kiosk_map local DB
            mysql_query("UPDATE access_codes SET bbid=0, occupied=0, parcel_num=0, email_count=0, redelivery=0 where compartment=" . $compartment);

            $conn = @fsockopen("www.google.com", 80, $errno, $errstr, 30);

            if($conn)
            {
                //Update the tracking system
                $link2 = mysql_connect("mysql.bufferbox.com", "bufferbox", "PASSWORD WOULD GO HERE") or die(mysql_error());
                mysql_select_db("tracking_system", $link2) or die(mysql_error());

                for($j = 0; $j < $parcels_in_compartment; $j++)
                {
                    //echo "Updating the tracking system for parcel num: " . $parcel_nums_array[$j] . "<br>";
                    mysql_query("INSERT INTO parcel_events (bb_id, parcel_num, event_id, timestamp) VALUES (".$bbid.", ".$parcel_nums_array[$j].", 3, '".date("Y-m-d H:i:s")."')", $link2);
                }

                mysql_select_db("abidadi_bb", $link2) or die(mysql_error());
                mysql_query("UPDATE wp_users SET credits=(credits-".$parcels_in_compartment."), UsedCredits=(UsedCredits+".$parcels_in_compartment.") WHERE ID = ".$bbid, $link2);
                mysql_close($link2);
            }/

            echo "Successful!";//'<META HTTP-EQUIV="Refresh" Content="0; URL=http://localhost/DoorOpened.html?compartment=' . $compartment . '">';

            break;
        }
        elseif($data == "closed:" . $selectedComp)
        {
            dio_write ($fd , "unlock:" . $selectedComp);
            dio_write ($fd , chr(13).chr(10));
        }
        elseif($data == "OK")
        {
        }
    }

    dio_write ($fd , "status:" . $selectedComp);
    dio_write ($fd , chr(13).chr(10));

    usleep(100000);
    $i++;
}

//The rection if the door failed to open
if(!$door_opened)
{
    echo "<font color=white><center><h1>Uh oh! Sorry, the door failed to open :(<br><br>Please email support@bufferbox.com or try again: <br><br>";
    echo "<a href='localhost'>Click Me to go back to home</a></h1></font></center>";
    echo '<META HTTP-EQUIV="Refresh" Content="30; URL=http://localhost">';

    $conn = @fsockopen("www.google.com", 80, $errno, $errstr, 30);

    if($conn)
    {

        /*Email uwaterloo@bufferbox.com to say that the door failed to open/

       require("phpmailer/class.phpmailer.php");

       function smtpmailer($mail, $to, $from, $from_name, $subject, $body) 
       {
           global $error;
           $mail->IsSMTP(); // enable SMTP
           $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
           $mail->SMTPAuth = true;  // authentication enabled
           $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
           $mail->Host = 'smtp.gmail.com';
           $mail->Port = 465;
           $mail->Username = "founders@bufferbox.com";
           $mail->Password = "PASSWORD WOULD GO HERE";
           $mail->SetFrom($from, $from_name);
           $mail->Subject = $subject;
           $mail->Body = $body;
           $mail->AddAddress($to);
           $mail->SMTPDebug = 1;
           $mail->IsHTML(true);
           if(!$mail->Send()) 
           {
               $error = 'Mail error: '.$mail->ErrorInfo;
               return false;
           } else 
           {
               $error = 'Message sent!';
               return true;
           }
        }

        $mail = new PHPMailer();

        //Email the appropriate user the updated information
        $to="uwaterloo@bufferbox.com"; // to who
        $from="uwaterloo@bufferbox.com";
        $from_name="uWaterloo BufferBox";
        $subject="uWaterloo SLC Kiosk: Door Failed To Open";
        $body = "Compartment: " . $compartment . "<br>The user entered this correct code: " . $access_code . " but the door failed to open (jam?). Please look into this so it doesn't happen again.";

        $mail->ClearAddresses();

        smtpmailer($mail, $to, $from, $from_name, $subject, $body);
    }
}

//Close the serial port
dio_close($fd);
	// Use result
	// Attempting to print $result won't allow access to information in the resource
	// One of the mysql result functions must be used
	// See also mysql_result(), mysql_fetch_array(), mysql_fetch_row(), etc.
	/*while ($row = mysql_fetch_assoc($result)) {
	   echo $row['firstname'];
    	echo $row['lastname'];
	    echo $row['address'];
	    echo $row['age'];
	}*/
//	CreateBoxTable($result);

	// Free the resources associated with the result set
	// This is done automatically at the end of the script
	//mysql_free_result($result);

//}

//function CreateBoxTable($result) {
	/*while ($row = mysql_fetch_assoc($result)) {
		$boxVals = 'Col: '.$row['Column_ID'].' | Box: '.$row['Box_ID'];
		
		if($row['Filled']=='1') {
			<form>
				<input type="button" value="$boxVals" enabled onClick="<? bb_OpenAction() ?>"/>
			</form>
			echo $boxVals . ' | FILLED!!!';
		} else {
			/*<form>
				<input type="button" value="$boxVals" onClick="<? bb_OpenAction() ?>"/>
			</form>
			echo $boxVals . ' | UNFILLED!!!';
		}
		
	}*/


mysql_free_result($result);

/*function bb_OpenAction() {
	echo "<script type='text/javascript'>alert('Really annoying pop-up!');</script>";
	//echo("This worked!");
	//Takes input from UI, barcode, or other input and selects box from db
	//bb_SelectBox($id);
		
	//bb_Send($msg);
	//bb_send should return a bool with status if sent or not

	//bb_Update($boxinfoandupdateinfo);
	//bb_update will change state of box in db
}*/
}		
?>
</head>
<body>
	<?php
		get_Size($_POST["pSize"]);
	?>
</body>
</html>