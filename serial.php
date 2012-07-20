<?php>
include 'php_serial.php';

class serial 
{
	// if os is linux /dev/ttys0 FIX THIS LATER
	var $com_string = '/dev/cu.usbmodem621';
	$serial = new phpserial();
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

	/* function will query the serial port until it returns a value.
		Nothing else can happen while querying the port.
	*/
	function readMsg()
	{
		do 
		{
			$result = $serial->readPort();
		} while ($result != chr(10));
		return $result;
	}
	
	/* msgType indicates the type of message being sent
		SL: will tell backend to get status for box of limit
		SS : will get status of sensor
		U: will send an unlock message to the box
	*/
	function writeMsg ($msgType, $boxID, $waitForReply = 0.1)
	{
		$str = $msgType . &boxID;
		$serial->sendMessage($str, $waitForReply);
	}
	
	function portClose()
	{
		$serial->deviceClose();
	}
}
?>
