<?
require_once('includes/utilities.php');
$link = db_connect();
require_once('includes/session.php');
require_once('includes/usermanagement.php');
if(isset($_SESSION['production'])) {
	$url = "login.php?production=" . $_SESSION['production'];
	include_once('includes/prodmanagement.php');
	include_once('includes/frames/prodtheme.php');
	$footer = true;

	$link = db_connect();
	$production = get_production($link, $_SESSION['production']);
	print_prod_header($link, $production);
} else {
	$url = "index.php";
	echo('<html><body>');
}

$_SESSION = array();
session_destroy();

include('includes/groundwork-header.php');
?>

<header>
<div class="header-container row">
	<div class="one third">
        <h1><a href="admin_login.php">RBS Admin</a></h1>
	</div>
	<div class="two thirds align-right">
	<a role="button" href="/admin_login.php">Login</a>
	</div>
</div>
</header>

<div class="container">

<h2>You have been logged out</h2>

<p>Thanks for being an awesome revue ticketing volunteer! <a href="/admin_login.php">Return to the login screen</a>.</p>

<? include('includes/page-footer.php'); ?>

