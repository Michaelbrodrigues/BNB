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

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT room.roomID, room.roomname, booking.Checkin, booking.Checkout, customer.firstname, customer.lastname FROM `booking`,`room`, `customer`WHERE booking.customerID = customer.customerID AND room.roomID = booking.roomID';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Booking list</h1>
<h2><a href='addbooking.php'>[Add a Booking]</a><a href="index.php">[Return to main page]</a></h2>
<table border="1">
<thead><tr><th>Booking (room, dates)</th><th>Customer</th><th>Action</th></tr></thead>
<?php

//makes sure we have rooms
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['roomID'];	
    echo '<tr><td>'.$row['roomname'].', '.$row['Checkin'].', '.$row['Checkout'].'</td><td>'.$row['firstname'].'</td>';
	  echo     '<td><a href="bookingview.php?id='.$id.'">[view]</a>';
	  
      //check if we have permission to modify data
      if (isAdmin()) {
        echo         '<a href="editbooking.php?id='.$id.'">[edit]</a>';
        echo         '<a href="managereviews.php?id='.$id.'">[manage reviews]</a>';
        echo         '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
      }
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No rooms found!</h2>"; //suitable feedback
echo "</table>";
mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done



echo '</div></div>';
require_once "footer.php";
?>

  