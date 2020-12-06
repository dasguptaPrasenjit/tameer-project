<?php
$status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];

$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt="yIEkykqEH3";
//DB connection and Update
require 'db_con.php';

If (isset($_POST["additionalCharges"])) {
   $additionalCharges=$_POST["additionalCharges"];
	$retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        
}else {	  

   $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);
  
if ($hash != $posted_hash) {
	mysqli_query($con, "UPDATE order_master SET transaction_status = 'Invalid' WHERE transaction_id = '".$txnid."'");
	mysqli_close($con);
    echo "Invalid Transaction. Please try again";
}
else {
	mysqli_query($con, "UPDATE order_master SET transaction_status = 'Failed' WHERE transaction_id = '".$txnid."'");
	mysqli_close($con);
	echo "<h3>Your order status is ". $status .".</h3>";
	echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment.</h4>";
  
} 
?>