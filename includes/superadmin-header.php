<header>
<div class="header-container">
    <? if (isset($production['name'])): ?>
        <h1><a href="admin_production.php">RBS Admin - <?=$production['name']?></a></h1>
    <? else: ?>
        <h1><a href="admin_production.php">RBS Admin</a></h1>
    <?endif?>
	<div class="row">
<<<<<<< HEAD
        <div class="one third">Logged in as: <?=$_SESSION['admin_name']?></div>
		<div class="two thirds align-right">
			<a role="button" href="admin_prodlist.php">Production list</a>
			<? if($_SESSION['admin_superadmin'] == 1) { ?><a role="button" href="admin_newproduction.php" class="medium button">Add production</a> <? } ?>
=======
        <div class="one third">Logged in as: <?=$_SESSION['admin_name']?> (<?=$_SESSION['admin_email']?>)</div>
		<div class="two thirds align-right">
			<? if($prodid >= 0) { ?>
			<a role="button" href="admin_production.php?production=<?=$prodid?>">Production Page</a>
			<? } else { ?>
			<a role="button" href="admin_prodlist.php">Production list</a>
			<? } ?>
			<? if($_SESSION['admin_superadmin']) { ?><a role="button" href="admin_newproduction.php" class="medium button">Add production</a> <? } ?>
>>>>>>> 7c88913439696cc491075c2d77076f44556efc87
			<a role="button" href="logout.php">Logout</a>
		</div>
	</div>
</div>
</header>

<div class="container">