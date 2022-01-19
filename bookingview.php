<!DOCTYPE HTML>
<?php
include "header.php";
include "menu.php";
include "checksession.php";
checkUser();
loginStatus();
echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">'; 
?>
<html><head><title>View Room</title> </head>
 <body>

<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//do some simple validation to check if id exists
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
 echo "<h2>Invalid Room ID</h2>"; //simple error feedback
 exit;
} 

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM `booking` WHERE   roomid='.$id ;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Room Details View</h1>
<h2><a href='listrooms.php'>[Return to the Room listing]</a><a href='index.php'>[Return to the main page]</a></h2>
<?php

//makes sure we have the Room
if ($rowcount > 0) {  
   echo "<fieldset><legend>Room detail #$id</legend><dl>"; 
   $row = mysqli_fetch_assoc($result);
   echo "<dt>Room name:</dt><dd>".$row['roomID']."</dd>".PHP_EOL;
   echo "<dt>Checking:</dt><dd>".$row['Checkin']."</dd>".PHP_EOL;
   echo "<dt>Checkout:</dt><dd>".$row['Checkout']."</dd>".PHP_EOL;
   echo "<dt>Contact Number:</dt><dd>".$row['ContactNumber']."</dd>".PHP_EOL;
   echo "<dt>Booking Extras:</dt><dd>".$row['BookingExtras']."</dd>".PHP_EOL;
   echo "<dt>Room Review:</dt><dd>".$row['RoomReview']."</dd>".PHP_EOL; 
   echo '</dl></fieldset>'.PHP_EOL;  
} else echo "<h2>No Room found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table>
</body>
<?php
echo '</div></div>';
require_once "footer.php";
?>
</html>
  