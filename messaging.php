<?php
include 'php_serial.php';

class messaging
{
	private static $com_string = '/dev/cu.usbmodem621';
	/* function will query the serial port until it returns a value.
		Nothing else can happen while querying the port.
	*/
	
	/*function getInstance() 
	{
		//the first time this class is instantiated the port will be setup. Otherwise do nothing
		if(!messaging::$instance = instanceof self) {
			messaging::$instance = new self();

	  		//Now we "open" the serial port so we can write to it 
		   messaging::$serial->deviceOpen();
		}
		
		return messaging::$instance;
	}*/
		/* ReadMsg returs a result based on the writeMsg sent. Results expected are:
		s: returns 2 bytes + null, MSB, forms unsigned num: 0-1023
		l: returns 2 bytes + null, MSB, forms unsigned num: 0-1023
		n: returns byte with number of boxes in column, end padded with zeros
		d: returns byte array of box sizes
	*/
	function readMsg()
	{
		$serial = new phpSerial();
		$serial->deviceSet($com_string);
		$serial->confBaudRate(9600); //Baud rate: 9600 
	 	$serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
	    $serial->confCharacterLength(8); //Character length 
	    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
	    $serial->deviceOpen();
	
		do 
		{
			$result = $serial->readPort();
		} while ($result != chr(10));
		
		$serial->deviceClose();
		return $result;
	}
	
	/* msgType indicates the type of message being sent
		U: Unlock
		S: Sensor/Analog Status
		L: Limit Switch Status
		N: New Column Address
	*/
	function writeMsg($msgType, $boxID)
	{
		$str = $msgType . $boxID . chr(10);
		
		$serial = new phpSerial();
		$serial->deviceSet('/dev/cu.usbmodem621');
		$serial->confBaudRate(9600); //Baud rate: 9600 
	 	$serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
	    $serial->confCharacterLength(8); //Character length 
	    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
	    $serial->deviceOpen();
		$serial->sendMessage($str);
		$serial->deviceClose();
	}
	
	function portClose()
	{
	
		$serial->deviceClose();
	}
}
?>
