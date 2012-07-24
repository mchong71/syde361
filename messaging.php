<?php
include 'php_serial.php';

$_THRESH = 100;
$_LOCKTHRESH = 100;
	    
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
		global $_THRESH, $_LOCKTHRESH;
		
		if($this->_SERIAL->_ckOpened() == false) { return "fail";}
		$this->_SERIAL->serialflush();
		
		$result = -5;
		$data = "";
		$i = 0;
		
		while (!preg_match("/[\s,]+/", $data))
		{
			if($i >=75000) {break;}
		
			$i++;
			$data .= $this->_SERIAL->readPort();
		}

		$arr = str_split($data);

		$resultID = $arr[0];


		if ($resultID == "s")
		{
			$value = (ord($arr[3])*256)+ord($arr[4]);
			
			if($value > $_THRESH)
				$result = 1; // Filled
			else 
				$result = 0; // UnFilled
		}
		elseif ($resultID == "l")
		{
			$value = (ord($arr[3])*256)+ord($arr[4]);

			if($value > $_LOCKTHRESH) 
				$result = 1; // UnLocked
			else 
				$result = 0; // locked
		}
		elseif ($resultID == "a")
		{
			$result = 1; // message was confirmed
		}
		elseif ($resultID == "n")
		{
			$iDplusCol = Array(0 => ord($arr[1]), 1 => ord($arr[2]));
			return $iDplusCol; // new column was found return the number of boxes
		}
		elseif ($resultID == "t")
		{
			$result= ord($arr[3]); // returns the size of queried box
		}
		
		return $result;
	}

	/* msgType indicates the type of message being sent
		U: Unlock
		S: Sensor/Analog Status
		L: Limit Switch Status
		A: New Column Address
	*/
	public function writeMsg($msgType, $col = 0, $box = 0)
	{
	    if($this->_SERIAL->_ckOpened() == false) { return "fail";}

	    $str = $msgType.chr($col).chr($box).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0).chr(0);
	
		$this->_SERIAL->sendMessage($str);
	}
	
	public function closeDevice() {
		if($this->_SERIAL->_ckOpened()) { 
			$this->_SERIAL->deviceClose();
		}
	}
}
?>
