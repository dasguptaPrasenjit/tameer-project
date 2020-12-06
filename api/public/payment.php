<?php
// Merchant key here as provided by Payu
$MERCHANT_KEY = "hDkYGPQe";

// Merchant Salt as provided by Payu
$SALT = "yIEkykqEH3";

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";

$action = '';

$posted = array();
if(!empty($_POST)) {
  //print_r($_POST);die();
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = "TXN".time().rand(10000,999999);
} else {
  $txnid = $posted['txnid'];
}

//DB connection and Update
require 'db_con.php';
mysqli_query($con, "UPDATE order_master SET transaction_id = '".$txnid."' WHERE order_id = '".$_REQUEST['orderid']."'");
mysqli_close($con);

$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
  } else {
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
	$hash = $posted['hash'];
	$action = $PAYU_BASE_URL . '/_payment';
}
function getSuccessUrl()
{
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return str_replace('payment.php','success.php',$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}
function getFailureUrl()
{
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return str_replace('payment.php','failure.php',$protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}
?>
<html>
  <head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    <br/>
    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <table>
        <tr>
          <td><input type="hidden" name="amount" value="<?php echo (empty($_REQUEST['amount'])) ? '' : $_REQUEST['amount'] ?>" /></td>
		</tr>
		<tr>
          <td><input type="hidden" name="firstname" id="firstname" value="<?php echo (empty($_REQUEST['name'])) ? '' : $_REQUEST['name']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="email" id="email" value="<?php echo (empty($_REQUEST['email'])) ? '' : $_REQUEST['email']; ?>" /></td>
		</tr>
		<tr>
          <td><input type="hidden" name="phone" value="<?php echo (empty($_REQUEST['phone'])) ? '' : $_REQUEST['phone']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="3"><input type="hidden" name="productinfo" value="<?php echo (empty($_REQUEST['orderid'])) ? '' : $_REQUEST['orderid'] ?>"/></td>
        </tr>
        <tr>
          <td colspan="3"><input type="hidden" name="surl" value="<?php echo getSuccessUrl();?>" size="64" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="furl" value="<?php echo getFailureUrl();?>" size="64" /></td>
        </tr>

        <tr>
          <td><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
        </tr>
      </table>
	  <div class="container">
		  <h2>Payment Details</h2>
		  <div class="list-group">
			<a href="javascript:void(0)" class="list-group-item active">
			  <h4 class="list-group-item-heading">Payment Details</h4>
			</a>
			<a href="#" class="list-group-item">
			  <h4 class="list-group-item-heading">Amount</h4>
			  <p class="list-group-item-text">INR : <?php echo (empty($_REQUEST['amount'])) ? '' : $_REQUEST['amount'] ?></p>
			</a>
			<a href="#" class="list-group-item">
			  <h4 class="list-group-item-heading">Name</h4>
			  <p class="list-group-item-text"><?php echo (empty($_REQUEST['name'])) ? '' : $_REQUEST['name']; ?></p>
			</a>
			<a href="#" class="list-group-item">
			  <h4 class="list-group-item-heading">Order Id</h4>
			  <p class="list-group-item-text"><?php echo (empty($_REQUEST['orderid'])) ? '' : $_REQUEST['orderid']; ?></p>
			</a>
		  </div>
		  <?php if(!$hash) { ?>
			<input type="submit" class="btn btn-success btn-block" value="Pay Now" />
		  <?php } ?>
		</div>
    </form>
  </body>
</html>