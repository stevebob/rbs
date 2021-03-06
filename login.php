<?
/**
 * The login page.
 */

// TODO: Need to make the webmaster and producers emails be generic 

include_once('includes/utilities.php');
include_once('includes/prodmanagement.php');
include_once('includes/frames/prodtheme.php');
include_once('includes/usermanagement.php');

if(isset($_GET['p']))
    $prodid = (int)$_GET['p'];
else
    $prodid = (int)$_GET['production'];

$link = db_connect();
include_once('includes/session.php');
if(!isset($_SESSION['user_id'])) {
    session_destroy();
}
if(!production_exists($link, $prodid))
	die('Production does not exist');

$production = get_production($link, $prodid);


if(isset($_GET['bookingref'])) {
    user_login_by_id($link, $production['id'], $_GET['bookingref']);
	header('Location: paymentsummary.php?production='.$production['id'].'&bookingref='.$_GET['bookingref']);
	exit;
}

include_once('includes/theatres/' . $production['theatre'] . '.inc');

include('includes/groundwork-header.php');
?>
<link type="text/css" rel="stylesheet" href="<?= $production['css']  ?>">
<div class="container">
<div class="header">
<h1 class="invisible"><?=$production['name']?></h1>

<p class="invisible">13-16 May 2014, 7:30pm, UNSW Science Theatre</p>
</div>
<div id="loginform">

<div id="register" class="box">
<h2>Book tickets</h2>
<p class="loginblurb">To make a new booking, please enter your details:</p>
<form method="post" action="register.php#main">
<div class="loginfield"><div class="loginlabel">First Name:</div><input type="text" name="fname" autofocus="autofocus" /></div>
<div class="loginfield"><div class="loginlabel">Last Name:</div><input type="text" name="lname" /></div>
<div class="loginfield"><div class="loginlabel">Email Address:</div><input type="text" name="email"/></div>
<?/*
<div class="loginfield"><div class="loginlabel">Password:</div><input type="password" name="pass" /></div>
<div class="loginfield"><div class="loginlabel">Repeat Password:</div><input type="password" name="pass_repeat" /></div>
*/?>
<div class="loginfield"><div class="loginlabel">Phone Number:</div><input type="text" name="phone" /></div>
<input type="hidden" name="production" value="<?=$prodid?>" />
<div class="loginsubmit"><input type="submit" value="Register" /></div>
</form>
</div>

<div id="login">
<div class="ticketprices">
<h3>Opening night $10</h3>
<h3>Arc members $10</h3>
<h3>Students $12</h3>
<h3>General Admission $15<h3>
<h3>Group tickets (10+ tickets) $10</h3>
</div>
<h2>Terms and conditions</h2>
<p>For large group bookings (10+ people), please <a href="mailto:<?= $production['salesemail'] ?>">contact the Sales and Ticketing Team</a></p>
<p>Upon payment, your tickets will be sent to the email address supplied. Once payment is made, under no circumstances will refunds be given.</p>
<p>Please print and present your ticket at the door. Paypal receipts will not be accepted.</p>
<p>A 60c booking fee will be added to each ticket booked online.</p>
<p>Your contact details will not be used for any purposes other than the following:</p>
<ul>
<li>To send you your booking details and notify you of any necessary changes to your booking.</li>
<li>To confirm your arrival in case you are running late.</li>
</ul>
<p>Your details will not be used for distribution of spam or for marketing purposes and will never be shared with any external entities without your prior permission.</p>
<p><? if($production['faqlocation'] == '') { ?>
Please <a href="mailto:<?=$production['salesemail']?>">contact the Sales Team</a> with any issues or equiries, or if you wish to modify a booking.
<? } else { ?>
Please read the <a href="<?=$production['faqlocation']?>">FAQ</a> or <a href="mailto:producers@cserevue.org.au">email the Sales Team</a> and we'll help as soon as we can!
</p>
<? } ?>
</div>

</div>
<div class="sponsors clear"></div>
</div>
</body>
</html>
