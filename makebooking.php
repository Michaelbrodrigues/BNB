<!DOCTYPE HTML>
<html>
<head>
<title>Make a Booking</title> 

<!-- below we have the Jquery CDN minified code -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" 
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <!-- below is the code for the date-range picker is the code for the -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Select a Date Range</title>
  <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">-->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->

  <!--this is the script for the data picker  -->
  <script>
  $( function() {
    var dateFormat = "mm/dd/yy",
    Checkin = $( "#Checkin" )
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 3
        })
        .on( "change", function() {
          Checkout.datepicker( "option", "minDate", getDate( this ) );
        }),
        Checkout = $( "#Checkout" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3
      })
      .on( "change", function() {
        Checkin.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
  } );
  function phonenumber(inputtxt)
  {
    var phoneno = /^\d{10}$/;
      if((inputtxt.value.match(phoneno)))
          {
            return true;
          }
        else
          {
          alert("Not a valid Phone Number");
            return false;
          }
  }
  </script>
</head>
 <body>

<?php
include "header.php";
include "menu.php";
include "checksession.php";
loginStatus(); //show the current login status

echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';

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

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the roomid from the URL
/*if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid room ID</h2>"; //simple error feedback
        exit;
    } 
}*/
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {     
//validate incoming data - only the first field is done for you in this example - rest is up to you do
    
//roomID (sent via a form ti is a string not a number so we try a type conversion!)    
    /*if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid room ID '; //append error message
       $id = 0;  
    }*/
$error = 0; //clear our error flag
$msg = 'Error: ';   
//roomname
if ( isset($_POST['BookingID']) || isset($_POST['Checkin']) || isset($_POST['Checkout']) || isset($_POST['ContactNumber']) || 
isset($_POST['BookingExtras']) || isset($_POST['roomID']) || isset($_POST['customerID']) || isset($_POST['Cooked']) || isset($_POST['Continental'])){
       
       //$BookingID = cleanInput($_POST['BookingID']);
//description
       $Checkin = cleanInput($_POST['Checkin']);        
//roomtype
       $Checkout = cleanInput($_POST['Checkout']);         
//beds
       $ContactNumber = cleanInput($_POST['ContactNumber']);
       
       $BookingExtras = cleanInput($_POST['BookingExtras']);
       
       $roomID = cleanInput($_POST['roomID']);

       //$customerID = cleanInput($_POST['customerID']);
       
       $breakfast = cleanInput($_POST['breakfast']);
} else {
  $error++; //bump the error flag
  $msg .= 'Something is wrong '; //append error message  
}  
//save the room data if the error flag is still clear and room id is > 0
    if ($error == 0) {
        $query = "INSERT INTO booking (BookingID,Checkin,Checkout,ContactNumber,BookingExtras,roomID,customerID,breakfast) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'issssiis', $BookingID, $Checkin, $Checkout, $ContactNumber,$BookingExtras,$roomID,$customerID,$breakfast,); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>New Booking successful.</h2>";     
//        header('Location: http://localhost/bit608/listrooms.php', true, 303);      
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    } 
    mysqli_close($DBC); //close the connection once done     
}
//locate the room to edit by using the roomID
//we also include the room ID in our form for sending it back for saving the data
/*$query = 'SELECT * FROM booking';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
*/
?>
<h1>Make a Booking</h1>
<h2><a href='listbookings.php'>[Return to the Booking listing]</a><a href='index.php'>[Return to the main page]</a></h2>
<?php
$roomID = array();
//makes sure we have rooms
while ($row = mysqli_fetch_assoc($result2)) {
  $roomID[] = $row;
}           
?>
<form method="POST" action="makebooking.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
  <p><span>Room (name,type,beds):</p>
  <label for="roomID">Choose a room:</label>
  <select name="roomID" id="roomID">
    <?php
      foreach($roomID as $room){
          echo "<option value=\"{$room['roomID']}\">{$room['roomname']},{$room['roomtype']},{$room['beds']}</option>";
      }
    ?>
  </select>

  <p>  
  <label for="breakfast">Breakfast type: </label>
  <input type="radio" id="cooked" name="breakfast" value="C"> Cooked 
  <input type="radio" id="continental" name="breakfast" value="CO"> Continental 
  </p>
          


  <!-- this is the HTML code for the datapicker-->
  <p>
  <label for="Checkin">Checkin date:</label>
  <input type="date" id="Checkin" name="Checkin">
  <label for="Checkout">Checkout date:</label>
  <input type="date" id="Checkout" name="Checkout">
  </p>

  <!-- THIS CODE BELOW IS CONECTED TO THE FUNCTION AND CHECKS IF THE PHONE IS VALID-->
  <p>
  <span>Contact Number:</span><input type='text'id="ContactNumber" name='ContactNumber' placeholder="(###) ###-####" onclick="phonenumber(document.form1.ContactNumber)" />
  </p>

  <p><span>Booking Extras:</span><textarea rows="8" cols="50" name="BookingExtras"></textarea></p>
   <input type="submit" name="submit" value="Add">
 </form>
<?php 
/*} else { 
  echo "<h2>room not found with that ID</h2>"; //simple error feedback
}*/

?>
</body>
<?php
echo '</div></div>';
require_once "footer.php";
?>
</html>
  