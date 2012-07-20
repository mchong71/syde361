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
	}
	
	/* ReadMsg returs a result based on the writeMsg sent. Results expected are:
		s: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 threshold value
		l: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 anything not zero is open
		d: returns byte array of box sizes
	*/
	public function readMsg()
	{
		$serial = new phpSerial();
		$serial->deviceSet('/dev/cu.usbmodem621');
		$serial->confBaudRate(9600); //Baud rate: 9600 
	 	$serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
	    $serial->confCharacterLength(8); //Character length 
	    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
	    $serial->deviceOpen();
	    
		do 
		{
			$data = $serial->readPort();
		} while ($result != chr(10));
		
		$arr = str_split($data);
		$resultID = $arr[0];
		
		if ($resultID = "s")
		{
			if($arr[1] <= $threshold) 
				$result = 0; // Unfilled
			else 
				$result = 1; // Filled
		}
		else if ($resultID = "l")
		{
			if($arr[1] == 0) 
				$result = 0; // Locked
			else 
				$result = 1; // Unlocked
		}
		else ($resultID = "d")
		{
			for($i = 0; $i < count($arr) && $arr[$i] =! 0; $i++)
			{
				$result[$i] = $arr[$i+1]; // Data starts at element 1
			}
		}
		return $result;
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
	public function writeMsg($msgType, $compartment)
	{
		$serial = new phpSerial();
		$serial->deviceSet('/dev/cu.usbmodem621');
		$serial->confBaudRate(9600); //Baud rate: 9600 
	 	$serial->confParity("none");  //Parity (this is the "N" in "8-N-1")
	    $serial->confCharacterLength(8); //Character length 
	    $serial->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
	    $serial->deviceOpen();
	    
		$str = $msgType . $compartment . chr(10);
		
		$serial->sendMessage($str);
		$serial->deviceClose();
	}
}
?>
