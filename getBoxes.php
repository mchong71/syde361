<?php
//echo("THIS IS A TEST!");
		
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

// Open connection to sql db
//function GetBoxes() {
	//Get COM Port
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("$dbhost", "$dbuser", "$dbpass") or die (mysql_error());
	mysql_select_db("$dbname") or die(mysql_error());
	
	// Formulate Query
	$query = sprintf("SELECT Package_ID, Column_ID, Box_ID, Filled, Open, Size FROM States",

	// Perform Query
	$result = mysql_query($query);

	// Check result
	// This shows the actual query sent to MySQL, and the error. Useful for debugging.
	if (!$result) {
    	$message  = 'Invalid query: ' . mysql_error() . "\n";
	    $message .= 'Whole query: ' . $query;
    	die($message);
	}

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
	//open connection to DB
	//query for all boxes (col, box, filled)
	//forreach in array create a button and based on filled it is on or off
		//add onClick action on each of the boxes that
	while ($row = mysql_fetch_assoc($result)) {
		$boxVals = 'Col: '.$row['Column_ID'].' | Box: '.$row['Box_ID'];
		
		if($row['Filled']=='1') {
			/*<form>
				<input type="button" value="$boxVals" enabled onClick="<? bb_OpenAction() ?>"/>
			</form>*/
			echo $boxVals . ' | FILLED!!!';
		} else {
			/*<form>
				<input type="button" value="$boxVals" onClick="<? bb_OpenAction() ?>"/>
			</form>*/
			echo $boxVals . ' | UNFILLED!!!';
		}
		
	}
//} 

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
}
	

function bb_Update($stuff) {
	//Opens connection to DB and writes to box line with status and other info
}*/
		
?>