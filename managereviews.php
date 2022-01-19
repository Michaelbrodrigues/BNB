<!DOCTYPE HTML>
<html><head><title>Edit a room</title> </head>
 <body>

<?php
include "header.php";
include "menu.php";
include "checksession.php";
checkUser();
loginStatus();
echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">'; 

include "config.php"; //load in any variables
$DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
  exit; //stop processing the page further
};

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the roomid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid room ID</h2>"; //simple error feedback
        exit;
    } 
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
//validate incoming data - only the first field is done for you in this example - rest is up to you do
    
//roomID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid room ID '; //append error message
       $id = 0;  
    }   
//roomname
       //$roomname = cleanInput($_POST['roomname']); 
//description
       //$description = cleanInput($_POST['description']);
//roomreview       
       $RoomReview = cleanInput($_POST['RoomReview']);
//roomtype
       //$roomtype = cleanInput($_POST['roomtype']);         
//beds
       //$beds = cleanInput($_POST['beds']);         
    
//save the room data if the error flag is still clear and room id is > 0
    if ($error == 0 and $id > 0) {
      //,roomname=?,description=?,roomtype=?,beds=?
        $query = "UPDATE booking SET RoomReview=? WHERE roomID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $RoomReview,$id);
        //mysqli_stmt_bind_param($stmt,'sssssi', $RoomReview,$roomname, $description, $roomtype, $beds, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Room details updated.</h2>";     
//        header('Location: http://localhost/bit608/listrooms.php', true, 303);      
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
//locate the room to edit by using the roomID
//we also include the room ID in our form for sending it back for saving the data
$query = 'SELECT  booking.roomID,room.roomtype, BookingID,Checkin,Checkout,ContactNumber,BookingExtras,RoomReview,room.beds FROM `booking`,`room` WHERE BookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);

?>
<h1>Room Details Update</h1>
<h2><a href='listrooms.php'>[Return to the room listing]</a><a href='index.php'>[Return to the main page]</a></h2>

<form method="POST" action="managereviews.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
   
  <p>
    <label for="RoomReview">Room Review: </label>
    <input type="text" id="RoomReview" name="RoomReview" size="60%" minlength="5" maxlength="200" value="<?php echo $row['RoomReview']; ?>" required> 
  </p>  
 
   <input type="submit" name="submit" value="Update">
 </form>
<?php 
} else { 
  echo "<h2>room not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
</body>
<?php
echo '</div></div>';
require_once "footer.php";
?>
</html>
  