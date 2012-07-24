<html>
<head>

<?php 
	$dbhost = "localhost";
	$dbname = "kiosk_map";
	$dbuser = "root";
	$dbpass = "root";
	$link = mysql_connect("localhost", "root", "root") or die (mysql_error());
	mysql_select_db("kiosk_map") or die(mysql_error());
	
function getState()
{
	$column = get_SQLarray("Select Max(Column_ID) as Column_ID from States");
	$box = get_SQLarray("Select Max(Box_ID) as Box_ID from States");
	echo "<table><tr>";
	$count;
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
					$color = "green";
				}
				else 
				{
					$filled = "N";
					$color = "red";
				}
				if ($coltrue != 0 && $boxtrue != 0)
					 echo '<td style="background-color: ' . $color . '">C:' . $j . " B:" . $i . " S:". $package['Size'] . " ";
				else 
						echo "<td>&nbsp;</td>";																		
			}
		echo "</tr>";
	}
	echo "</tr></table>";
}
	
function get_SQLarray($query) 
{
	$result = mysql_query($query);
	$array = mysql_fetch_array($result);
	mysql_free_result($array);
	return $array;
}
	
?>

</head>
<body>
<?php getState();?>
</body>
</html>