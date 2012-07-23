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
		$this->_SERIAL->deviceOpen();
	}
	
	/* ReadMsg returs a result based on the writeMsg sent. Results expected are:
		s: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 threshold value
		l: returns 2 bytes + null, MSB, forms unsigned num: 0-1023 anything not zero is open
		d: returns byte array of box sizes
	*/
	public function readMsg()
	{
		global $_THRESH;
		
		if($this->_SERIAL->_ckOpened() == false) { return "fail";}
		$this->_SERIAL->serialflush();
		
		$result = -1;
		$data = "";
		$i = 0;
		
		while (!preg_match("/[\s,]+/", $data))
		{
			if($i >=75000) {break;}
		
			$i++;
			$data .= $this->_SERIAL->readPort();
		}
		
		return $data."--".$i;
		/*$arr = str_split($data);
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
		elseif ($resultID == "a")
		{
				if($arr[1] == "0")
					$result = 0; // message was confirmed
				else 
					$result = 1; // message was not confirmed
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
		
		return $result;*/		
	}

	/* msgType indicates the type of message being sent
		U: Unlock
		S: Sensor/Analog Status
		L: Limit Switch Status
		A: New Column Address
	*/
	public function writeMsg($msgType, $compartment = -1)
	{
	    if($this->_SERIAL->_ckOpened() == false) { return "fail";}
	    $str;
	    
	    if ($compartment == -1) {
	    	$str .= $msgType. chr(10);
	    } else {
			$str = $msgType . $compartment; 
		}
	
		$this->_SERIAL->sendMessage($str);
	}
	
	public function closeDevice() {
		if($this->_SERIAL->_ckOpened()) { 
			$this->_SERIAL->deviceClose();
		}
	}
}
?>
