<?php
include 'php_serial.php';

$_THRESH = -1;
	    
class messaging
{
	private static $com_string = '/dev/cu.usbmodem621';
	private $_SERIAL;
	
	function messaging() { 
		$this->_SERIAL = new phpSerial();
		$this->_SERIAL->deviceSet('/dev/cu.usbmodem621');
		$this->_SERIAL->confBaudRate(9600); //Baud rate: 9600 
		$this->_SERIAL->confParity("none");  //Parity (this is the "N" in "8-N-1")
		$this->_SERIAL->confCharacterLength(8); //Character length 
		$this->_SERIAL->confStopBits(1);  //Stop bits (this is the "1" in "8-N-1") 
	}
	
	/* ReadMsg returs a result based on the writeMsg sent. Results expected are:
		s: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 threshold value
		l: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 anything not zero is open
		d: returns byte array of box sizes
	*/
	public function readMsg()
	{
		global $_THRESH;
		$result = -1;
		
	    //if(!$this->_SERIAL->deviceOpen()) { return $result;
	    $this->_SERIAL->deviceOpen();
	    //$serial->serialflush();
		$data = "";
		$i = 0;
		while (!preg_match("/[\s,]+/", $data))
		{
			$i++;
			usleep(250);
			$data = $this->_SERIAL->readPort();
			//$arr = preg_split("/[\s,]+/", $data);
		}
		
		return $data.$i;
		$arr = str_split($data);
		$resultID = $arr[0];
		
		if ($resultID == "s")
		{
			if($arr[1] <= $_THRESH)
				$result = 0; // Unfilled
			else 
				$result = 1; // Filled
		}
		elseif ($resultID == "l")
		{
			if($arr[1] == "0") 
				$result = 0; // Locked
			else 
				$result = 1; // Unlocked
		}
		elseif ($resultID == "d")
		{
			$result = $arr;
		}
		elseif ($resultID == "a")
		{
			foreach ($i = 1; $i < Count($arr); $i++)
			{
				if($arr[$i] == "0")
					$result = true; // message was confirmed
				else 
					$result = false; // message was not confirmed
				}
		}
		elseif ($resultID == "n")
		{
			if($arr[1] != "0")
				$result = $arr[1]; // new column was found return the number of boxes
			else 
				$result = -1; // new column was not found 
				
		}
		elseif ($resultID == "t")
		{
			$result= $arr[1]; // returns the size of queried box
		}		
		
		return $result;
		$this->_SERIAL->deviceClose();
		
	}

	/* msgType indicates the type of message being sent
		U: Unlock
		S: Sensor/Analog Status
		L: Limit Switch Status
		N: New Column Address
	*/
	public function writeMsg($msgType, $compartment = -1)
	{
		
	    //if(!$this->_SERIAL->deviceOpen()) {return false;}
	    $this->_SERIAL->deviceOpen();
	    $str;
	    
	    if ($compartment == -1) {
	    	$str .= $msgType. chr(10);}
	    else {
			$str .= $msgType . $compartment . chr(10); }
	
		
		$this->_SERIAL->sendMessage($str);
		$this->_SERIAL->deviceClose();
	}
}
?>
