<?
/**
 * Displays the payment summary, accepts the ticket price data from bookingsummary.php.
 * If the ticketing information is incomplete then push the user back to the booking summary page.
 * If there have been no bookings push the user straight to the bookings page.
 */

include_once('includes/utilities.php');
$link = db_connect();
include_once('includes/session.php');
include_once('includes/session.php');
include_once('includes/usermanagement.php');

if(isset($_GET['bookingref']) && isset($_GET['production'])) {
    user_login_by_id($link, $_GET['production'], $_GET['bookingref']);
}



include_once('includes/userauth.php');

include_once('includes/prodmanagement.php');
include_once('includes/pricemanagement.php');
include_once('includes/paymentmanagement.php');
include_once('includes/perfmanagement.php');
include_once('includes/frames/paymentsummary.php');
include_once('includes/frames/prodtheme.php');
include_once('paypalbutton.php');


$user = $_SESSION['user_id'];
$userinfo = get_user($link, $user);

$production = get_production($link, $_SESSION['production']);

// Get the prices from the booked seats
if(isset($_POST['price'])) {
	foreach($_POST['price'] as $seat => $price) {
		set_price_user($link, $user, $seat, $price);
	}
}

$ps = get_payment_summary($link, $_SESSION['user_id']);
if(!$ps || count($ps) == 0) {
	header('Location: booking.php');
	exit;
}

$htmlheaders = <<<HEADER
<script type="text/javascript" src="js/paymentsummary.js" ></script>
<script src="js/jquery.js" type="test/javascript"></script>
<script src="js/jquery.anchor.js" type="test/javascript"></script>
<link rel="stylesheet" type="text/css" href="$production[css]" />
HEADER;

//print_prod_header($link, $production, $htmlheaders);

include('includes/groundwork-header.php');
?>
<link type="text/css" rel="stylesheet" href="<?=$production['css']?>">
<div class="container">
<div class="header">
<h1 class="invisible"><?=$production['name']?></h1>

<p class="invisible">13-16 May 2014, 7:30pm, UNSW Science Theatre</p>
</div>
<h1 class="align-center">Payment Summary</h1>
<?
$userinfo = get_user($link, $user);
?>
<h2 class="align-center"><?=$userinfo['name']?> (<?=$userinfo['email']?>)</h2>
<!--p id="booktickets"><a href="booking.php">Click here to modify your bookings.</a></p-->

<?
ob_start();
$total = 0;
foreach($ps as $perfid => $summary) {
	// If there's no payment summary yet then don't display it.
	$displayps = false;
	foreach($summary as $sc) {
		foreach($sc as $row) {
			if($row['num'] > 0){
				$displayps = true;
				break;
			}
		}
	}
	if(!$displayps)
		continue;
	$perf = get_performance($link, $perfid);
	echo("<div class='paymentsummaryperf'>");
//	echo("<h2>" . $perf['title'] . "</h2>");
	print_payment_summary($link, $summary);

	if(isset($summary[1])) {
		foreach($summary[1] as $row) {
			$total += $row['price'] * $row['num'];
		}
	}
	echo("</div>");
}
?>
<a name='total'></a>

<div id="totals">
<p>Note that a $1 booking fee has been applied to cover the fee paypal charges us.</p><?/* paypalfee */?>
<? if ($total == 0): ?>
<h2>All tickets have been paid for.</h2>
<? else: ?>
<h2 class="paymenttotal">Total Amount Due: <strong>$<?=$total + 1?></strong></h2><?/* paypalfee */?>
<?endif?>
<?php
$paymentsummary = ob_get_contents();
ob_end_flush();
?>

<?php if ($total > 0): ?>
<!--<a name='options'></a>
<div id="paymentoptions">
<?
//if($production['acceptsales'] == 1) echo('<a class="paymentoption button large" id="paybysales" href="#total" onClick="togglePay(\'sales\');">Sales Desk</a>');
//if($production['acceptdd'] == 1) echo('<a class="paymentoption large button" id="paybydd" href="#total" onClick="togglePay(\'dd\');">Bank Transfer</a>');
if($production['acceptpaypal'] == 1) echo('<a href="#total" class="paymentoption large button" onClick="togglePay(\'paypal\');">Pay now via Paypal</a>');
?>-->
</div>
<a name="anchor" id="anchor"></a>
<?
//if($production['acceptsales']) {
//	$salesinfo = $production['salesinfo'];
//	$salesinfo = str_replace('{paymentid}', $userinfo['paymentid'], $salesinfo);
//	echo('<div class="pay" id="salesinfo"><a name="sales"></a><h4>Sales Desk</h4>' . $salesinfo . '</div>');
//}
if($production['acceptdd']) {
	$ddinfo = $production['ddinfo'];
	$ddinfo = str_replace('{paymentid}', $userinfo['paymentid'], $ddinfo);
	echo('<div class="pay" id="ddinfo"><a name="dd"></a><h4>Direct Debit</h4>' . $ddinfo . '</div>');
}
if($production['acceptpaypal']) {
	echo('<div class="pay" id="paypalinfo">Click the Paypal button below to purchase your tickets now!');
	if($total > 0) {
		$userRow = get_user($link, $user);
		generate_paypal_button($production, $userRow['paymentid'], $total);
	}
	echo('</div>');
}
?>
<?/*<a href="booking.php#main" class="button large">Modify Booking</a>*/?>
</div>

</div>

<?php

endif;

print_prod_footer($link, $production);

if(isset($_POST['price'])) {
    // Write a confirmation email to the user
    $email = "Dear ".$userinfo['name'].",<br/><br/>";
    $email .= "Thank you for booking your tickets for <strong>".$production['name']."</strong>!<br/>Below you will find a summary of your booking, as well as information on how to proceed.<br/><br/>";
    $email .= "<strong style='color:blue'>Please ensure you have paid before your booking expires, otherwise your seats may not be guaranteed.</strong>";
    $email .= "<br/><em><strong>Booking ID:</strong> ".strtoupper($userRow['paymentid'])."</em><br/>";
    $email .= $paymentsummary;
    $email .= "<br/><h3>Payment Options</h3>";
    $email .= "Please visit ".$production['bookingslocation']."/login.php?production=".$production['id']."&bookingref=".$user." to view your payment options.<br/>";
    $email .= "<br/><br/>If you have any queries, please feel free to respond to this email.<br/><br/>";
    send_email($userinfo['email'], "Your booking for ".$production['name']." [booked at ".date('g:ia')."]", $email, 
    "Content-Type: text/html; charset=ISO-8859-1\r\nFrom: ".$production['salesemail']."\r\n");
}
?>
