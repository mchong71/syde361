<html>
<head>

<?php

$sizeArr = array(0 => 1, 1 => 1, 2 => 1, 3 => 2, 4 =>2);

//Get COM Port
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());

	
function addBox($numOfBox, $sizeArr) {

	//get the next available column_ID
	// HAVE TO REFRESH PAGE TO GET THE NEW MAX
	$column_ID = get_SQLarray("Select MAX(Column_ID) as Column_ID from States");
	$max_ID = $column_ID['Column_ID'];
	echo $max_ID;
	//inserts all the new boxes into the database. Box_ID is 1 based
	for ($i = 0; $i < $numOfBox; $i++)
	{
		$boxNum = $i + 1;
		$new_ID = $max_ID + 1;
		mysql_query("Insert into States values (0," . $new_ID . "," . $boxNum . ",0,0," . $sizeArr[$i] . ")"); 
	}

}

function get_SQLarray($query){
	$result = mysql_query($query);
	$array = mysql_fetch_array($result);
	return $array;
}
?>
</head>
<body class="page_bg">

<!-- addBox could only take in an array where the first element is the number of
		boxes the next elements are the corresponding sizes -->
<Input type = 'button' Name = 'button1' onclick = "<? addBox(5, $sizeArr) ?>" Value = "Add box"/>

</body>
</html>



