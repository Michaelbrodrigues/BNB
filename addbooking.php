 <!DOCTYPE HTML>
<html><head><title>Add a new room</title> </head>
 <body>

<?php
include "header.php";
include "menu.php";
include "checksession.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}


//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
//if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
    include "config.php"; //load in any variables
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };
    //prepare a query and send it to the server
    $query2 = 'SELECT roomID,roomname,roomtype,beds FROM room ORDER BY roomtype';
    $result2 = mysqli_query($DBC,$query2);
    $rowcount2 = mysqli_num_rows($result2);
    //In here load the rooms into the array
    


//validate incoming data - only the first field is done for you in this example - rest is up to you do
//roomname
    $error = 0; //clear our error flag
    $msg = 'Error: ';
    if (isset($_POST['roomID']) and !empty($_POST['roomID']) and is_string($_POST['roomID'])) {
       $fn = cleanInput($_POST['roomID']); 
       $roomID = (strlen($fn)>50)?substr($fn,1,50):$fn; //check length and clip if too big
       //we would also do context checking here for contents, etc       
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid roomID '; //append eror message
       $roomID = '';  
    } 
 
           $roomID = cleanInput($_POST['roomID']); 
    //description
           $Checkin = cleanInput($_POST['Checkin']);        
    //roomtype
           $Checkout = cleanInput($_POST['Checkout']);         
    //beds
           $ContactNumber = cleanInput($_POST['ContactNumber']);
           
           $BookingExtras = cleanInput($_POST['BookingExtras']); 
    
           $customerID = cleanInput($_POST['customerID']);
           
           $Cooked = cleanInput($_POST['Cooked']);

           $Continental = cleanInput($_POST['Continental']);
       
//save the room data if the error flag is still clear
    if ($error == 0) {
      $query = "INSERT INTO booking (roomID,Checkin,Checkout,ContactNumber,BookingExtras,customerID,Cooked,Continental) VALUES (?,?,?,?,?,?,?,?)";
      mysqli_query($DBC,$query);
      $stmt = mysqli_prepare($DBC,$query); //prepare the query
      mysqli_stmt_bind_param($stmt,'issssiss', $roomID,$Checkin, $Checkout,$ContactNumber,$BookingExtras,$customerID,$Cooked,$Continental); 
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt); 
      echo "<h2>New room added to the list</h2>";       
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    } 
         
    //mysqli_close($DBC); //close the connection once done
}
$query = 'SELECT * FROM booking';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>
<h1>Add a new room</h1>
<h2><a href='listrooms.php'>[Return to the room listing]</a><a href='/bnb/'>[Return to the main page]</a></h2>
<?php

$rooms = array();
//makes sure we have rooms
while ($row = mysqli_fetch_assoc($result2)) {
  $rooms[] = $row;
}           
?>
<form method="POST" action="Makeabooking.php"> 

<p><span>Room (name,type,beds):</p>
<label for="rooms">Choose a room:</label>
<select name="rooms" id="rooms">
<?php
  foreach($rooms as $room){
      echo "<option value=\"{$room['roomID']}\">{$room['roomname']},{$room['roomtype']},{$room['beds']}</option>";
  }
?>
</select>

<p>  
<label for="breakfast">Breakfast type: </label>
<input type="radio" id="cooked" name="cooked" value="cooked"> Cooked 
<input type="radio" id="continental" name="continental" value="continental" Checked> Continental 
</p>
      


<!-- this is the HTML code for the datapicker-->
<p>
<label for="from">Checkin date:</label>
<input type="text" id="from" name="from">
<label for="to">Checkout date:</label>
<input type="text" id="to" name="to">
</p>

<!-- THIS CODE BELOW IS CONECTED TO THE FUNCTION AND CHECKS IF THE PHONE IS VALID-->
<p>
<span>Contact Number:</span><input type='text'id="phone" name='phone' placeholder="(###) ###-####" onclick="phonenumber(document.form1.phone)" />
</p>

<p><span>Booking Extras:</span><textarea rows="8" cols="50" name="bookingextra"></textarea></p>
<input  type="submit" name="submit" value="Add"/>
<p><a href="#">Cancel</a></p>
    
</form>
<?php
} else { 
  echo "<h2>room not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
<!--<form method="POST" action="addbooking.php">
  <p>
    <label for="rooms">Room name: </label>
    <select name="rooms" id="rooms">
      <?php         
        foreach($rooms as $room){
            echo "<option value=\"{$room['roomname']}\">{$room['roomname']},{$room['roomtype']},{$room['beds']}</option>";
        }
      ?>
  </select>
  </p> 
  <p>
    <label for="ContactNumber">ContactNumber: </label>
    <input type="text" id="ContactNumber" name="ContactNumber" minlength="5" maxlength="50" required> 
  </p> 
  <p>
    <label for="description">Description: </label>
    <input type="text" id="description" size="100" name="description" minlength="5" maxlength="200" required> 
  </p>  
  <p>  
    <label for="roomtype">Room type: </label>
    <input type="radio" id="roomtype" name="roomtype" value="S"> Single 
    <input type="radio" id="roomtype" name="roomtype" value="D" Checked> Double 
   </p>
  <p>
    <label for="beds">Beds (1-5): </label>
    <input type="number" id="beds" name="beds" min="1" max="5" value="1" required> 
  </p> 
  
   <input type="submit" name="submit" value="Add">
   </form>-->

  </body>
<?php
  echo '</div></div>';
require_once "footer.php";
?>
  </html>
  