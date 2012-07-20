<?php
include 'php_serial.php';

class messaging
{
	$portCreated = false;
	/* function will query the serial port until it returns a value.
		Nothing else can happen while querying the port.
	*/
	
	function messaging() 
	{
		//the first time this class is instantiated the port will be setup. Otherwise do nothing
		if(!$portCreated) {
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
		    
		    //successful open of port
		    $portCreated = true;
	    }
	}
	/* ReadMsg returs a result based on the writeMsg sent. Results expected are:
		s: returns 2 bytes + null, MSB, forms unsigned num: 0-1023
		l: returns 2 bytes + null, MSB, forms unsigned num: 0-1023
		n: returns byte with number of boxes in column, end padded with zeros
		d: returns byte array of box sizes
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
		U: Unlock
		S: Sensor/Analog Status
		L: Limit Switch Status
		N: New Column Address
	*/
	function writeMsg ($msgType, $boxID, $waitForReply = 0.1)
	{
		$str = $msgType . $boxID . chr(10);
		$serial->sendMessage($str, $waitForReply);
	}
	
	function portClose()
	{
		$serial->deviceClose();
	}
}
?>
