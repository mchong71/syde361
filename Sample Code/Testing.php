<html>
<head></head>
<body>
<button type="button" onClick="bb_OpenAction()">Click Me!</button>

<?php
/*Spec

Inputs
	web page interface
	feedback from back end (state)

Functions
Determine type of input
	Input from UI
		Determine what box is avail
		send signal to backend

	Input from backend
		Update state table
		display messages on UI 
		Receive/Delivered box or added lockers
*/

// Reposible for the entire opening box action (Open Controller)
function bb_OpenAction() {
	echo("This worked!");
	//echo "<script type='text/javascript'>alert('Really annoying pop-up!');</script>";
	//Takes input from UI, barcode, or other input and selects box from db
	//bb_SelectBox($id);
	
	//bb_Send($msg);
	//bb_send should return a bool with status if sent or not
	
	//bb_Update($boxinfoandupdateinfo);
	//bb_update will change state of box in db
}

function bb_SelectBox(%id) {
	//DB data will include the code/id of box that we want to open
	
}

function bb_Send($msg) {
	//msg as dummy
		//Will hold the box reference pulled from db
		//Higher level function will determine which box to send to; this simply sends
		//Needs to write to serial
		//Error Handling if doesn't send. Use separate method or some type of event
		
		// =>send("u 24 4") # unlock column 24, cell 4
}

funciton bb_Update($stuff) {
	//Opens connection to DB and writes to box line with status and other info
}


// The reaction if the door failed to open
/*function bb_FailedOpen() {
    echo "<font color=white><center><h1>Uh oh! Sorry, the door failed to open :(<br><br>Please email support@bufferbox.com or try again: <br><br>";
    echo "<a href='localhost'>Click Me to go back to home</a></h1></font></center>";
    echo '<META HTTP-EQUIV="Refresh" Content="30; URL=http://localhost">';

    $conn = @fsockopen("www.google.com", 80, $errno, $errstr, 30);

    if($conn)
    {

        //Email uwaterloo@bufferbox.com to say that the door failed to open

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
}*/

?>
</body>
</html>