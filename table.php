<html>
<head>
<style type="text/css">

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  z-index: 0;
}

img#bg {
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
}

body {
 font-family: sans-serif;	
}

#cell {
padding: 20px 10px 20px 10px;
}

#content {
  position:relative;
  z-index:9000;
}
</style>

<?php 
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());
	
function getStateAlt(){
	$columnD = get_SQLarray("Select distinct Count(Column_ID) as countOfCol from States");
	$numcol = $columnD['countOfCol'];
	$column = get_SQLarray("Select Max(Column_ID) as Column_ID from States");
	$box = get_SQLarray("Select Max(Box_ID) as Box_ID from States");
	echo '<table style="color: white; border-collapse: collapse; width: 80%; margin-left: auto; margin-right: auto; margin-top: 40px; font-size: large"><tr VALIGN = TOP>';
	$count = 0;
	$width = 100/($numcol);
	for ($z = 1; $z <= $column['Column_ID']; $z++){
		$colresult1 = mysql_query("Select Column_ID from States where Column_ID = ". $z);
		$coltrue1 = mysql_num_rows($colresult1);
		if ($coltrue1 != 0){
			echo '<td width="' . floor($width) . '%"><table id="'.$z.  '" style="color: white; border-collapse: collapse; width: 100%; margin-left: auto; margin-right: auto; margin-top: 40px; font-size: large">';
			for ($i = 1; $i <= $box['Box_ID']; $i++){
				$filled;
				$color;
				$coltrue = mysql_num_rows($colresult);
				$boxresult = mysql_query("Select Box_ID from States where Box_ID = " . $i . " AND Column_ID = " . $z);
				$boxtrue = mysql_num_rows($boxresult);
				$package = get_SQLarray("Select Filled, Size from States where Column_ID = " .$z . " AND Box_ID = " . $i);
				if ($package['Filled'] == 1){
					$filled = "Y";
					$color = "#0BA14B";
				} else {
					$filled = "N";
					$color = "#FF312D";
				}
				$cellsize;
					if ($package['Size'] == 0){
						$cellsize = 20;
					} if ($package['Size'] == 1){
						$cellsize = 30;
					} if ($package['Size'] >= 2){
						$cellsize = 40;
					}
				if ($boxtrue != 0){
								echo '<tr><td id="cell" height = '.$cellsize.' style="padding-top: ' . $cellsize . '; padding-bottom: ' . $cellsize . '; border: 1px solid black; background-color: '.$color.';">';
				echo '&nbsp;';
				
				echo "</td></tr>";
				}	

			} 
					echo '</table></td>';

		}
	}
	
	
	echo '</tr></table>';
}	
	
/*function getState()
{
	$column = get_SQLarray("Select Max(Column_ID) as Column_ID from States");
	$box = get_SQLarray("Select Max(Box_ID) as Box_ID from States");
	echo '<table style="color: white; border-collapse: collapse; width: 80%; margin-left: auto; margin-right: auto; margin-top: 40px; font-size: large">';
	$count = 0;
	for($z = 1; $z <= $column['Column_ID']; $z++){
		$colresult1 = mysql_query("Select Column_ID from States where Column_ID = ". $z);
		$coltrue1 = mysql_num_rows($colresult1);
		if ($coltrue1 != 0){
			echo '<th>Column ' . $z . '</th>';
		}
	}
	for ($i = 1; $i <= $box['Box_ID']; $i++)
	{	
		echo "<tr>";
			for($j = 1; $j <= $column['Column_ID']; $j++)
			{
				$filled;
				$color;
				$colresult = mysql_query("Select Column_ID from States where Column_ID = ". $j);
				$boxresult = mysql_query("Select Box_ID from States where Box_ID = " . $i . " AND Column_ID = " . $j);
				//echo "Box_ID " . $i;
				//echo "Col_ID " . $j;
				$boxtrue = mysql_num_rows($boxresult);
				$coltrue = mysql_num_rows($colresult);
				$package = get_SQLarray("Select Filled, Size from States where Column_ID = ".$j . " AND Box_ID = ".$i);
				if($package['Filled'] == 1)
				{
					$filled = "Y";
					$color = "#0BA14B";
				}
				else 
				{
					$filled = "N";
					$color = "#FF312D";
				}
				if ($coltrue != 0 && $boxtrue != 0){
					// echo '<td id="cell" style=" border: 1px solid black; background-color: ' . $color . '">C:' . $j . " B:" . $i . " S:". $package['Size'] . " ";
					echo '<td id="cell" style=" border: 1px solid black; background-color: ' . $color . '">'; // S: '. $package['Size'] . " ";
					
					if ($package['Size'] == 0){
						echo '&#9632;</td>';
					} if ($package['Size'] == 1){
						echo '&#9632;&#9632;</td>';
					} if ($package['Size'] == 2){
						echo '&#9632;&#9632;&#9632;</td>';
					}
				}
				else if ($coltrue != 0)
					echo '<td id="cell" style="text-align: center; background-color: #C4D5AB; color: grey" >Empty</td>';	
				//else 
						//echo '<td id="cell" style="background-color: #C4D5AB; color: grey" >Empty</td>';																		
			}
			$count = 1;
		echo "</tr>";
	}
	echo "</table>";
}*/
	
function get_SQLarray($query) 
{
	$result = mysql_query($query);
	$array = mysql_fetch_array($result);
	mysql_free_result($array);
	return $array;
}
	
?>

</head>
<body	style="background-image:url('../images/rebel.png');"> 
	<img src="GreenBackground.jpg" alt="background image" id="bg" />
	<div id="content">
	<?php getStateAlt();?>
	</div>
</body>
</html>