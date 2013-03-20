<?php
if ($_POST['uname'] == "" || $_POST['upass'] == "") {
	// Empty. :<
	header('Location: ../?p=login&id=1');
}
include('../inc/core.php');
$user = htmlentities($_POST['uname']);
$pass = $_POST['upass']; // Cant HTMLEntities it incase it contains symbols.

$res = $core->check_login($user,$pass);
if ($res) {
	$_SESSION['anope_id'] = $res;
	if (isset($_POST['ref']) && $_POST['ref'] != "") {
		header("Location: ".$_POST['ref']);
	}
	else { header('Location: ../?p=home'); }
}
else {
	header('Location: ../?p=login&id=2');
}
?>