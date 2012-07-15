<html>
<head>
<?php
echo("THIS IS A TEST!");
		
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
}
		
?>
</head>
	
<body>
	<button type="button" onclick="<? bb_OpenAction() ?>">Click Me!</button>
	
</body>
</html>