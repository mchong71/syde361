<?php

//Get COM Port
//MADE A CHANGE!
$link = mysql_connect("localhost", "root", "PASSWORD WORD GO HERE") or die(mysql_error());
mysql_select_db("kiosk_map") or die(mysql_error());
$result = mysql_query("SELECT data FROM config WHERE description='port'");
$row = mysql_fetch_array($result);
$com_string = 'COM' . $row['data'];
mysql_close($link);

function bb_read($fd) {

	$response = "";
	$data = dio_read($fd, 1);

	while($data != chr(10))
	{
		$response = $response . $data;
		$data = dio_read($fd, 1);
	}

	return $response;
}

exec('mode ' . $com_string . ': baud=9600 data=8 stop=1 parity=n xon=on');

$fd = dio_open($com_string, O_RDWR);

if(!$fd)
{
    die("Error when open " . $com_string);
}

dio_write ($fd , "unlock:" . $compartment);
dio_write ($fd , chr(13).chr(10));

$door_opened = 0;

$i=0;
while($i < 20)
{
    $data = bb_read($fd);

    if($data)
    {
        $data = trim($data);

        if($data == "opened:" . $compartment)
        {
            $door_opened = 1;

            mysql_connect("localhost", "root", "") or die(mysql_error());
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
            }

            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=http://localhost/DoorOpened.html?compartment=' . $compartment . '">';

            break;
        }
        elseif($data == "closed:" . $compartment)
        {
            dio_write ($fd , "unlock:" . $compartment);
            dio_write ($fd , chr(13).chr(10));
        }
        elseif($data == "OK")
        {
        }
    }

    dio_write ($fd , "status:" . $compartment);
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

        /*Email uwaterloo@bufferbox.com to say that the door failed to open*/

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


?>