<html>

<style type="text/css">

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

img#bg {
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
}

#content {
  position:relative;
  z-index:1;
}

</style>

<body style="margin:0 auto; text-align=center;padding:50px;color:#fff;font:12px Arial;">
<img src="GreenBackground.jpg" alt="background image" id="bg" />

<body>
<div id="content">

<p><b><FONT size ="16"><big>Package Retrieval</b></FONT></p></br>

  <form action="getBoxes.php" method="post">
    <b>Box Size (S/M/L):</b>
    </br><input type="text" name="pSize" style="width:150px; height:20px;" /><br><br>
    </br><b>Enter Specific Box:</b>
    </br> Column: <input type="text" name="column" style="width:150px; height:20px;"/><br>
    </br> Box: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="box" style="width:150px; height:20px;"/>
    </br><br>
<input type="submit" style="width:80px; height:130px;"/>
  </form>

  <!-- <form action="pickup.php" method="post">
 <p><b><FONT size ="12"><CENTER> <big>PICKUP</b></big></CENTER></FONT></p>
Please Enter your Package ID from BB: <input type="text" name="packageID" />
<input type="submit" />

</form> -->
<!-- <form action="getBoxes.php" method="post">
<FONT SIZE=+1 COLOR="white">Box Size (S/M/L): </FONT><input type="text" name="pSize"  style="width:150px; height:20px;"/><br><br><br>
<input type="submit" style="width:80px; height:130px;" />
</form> -->

</body>
</html>