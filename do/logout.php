<?php
session_start();
if (isset($_SESSION['anope_id'])) {
	unset($_SESSION['anope_id']);
}
if ($_SERVER['HTTP_REFERER'] == "" || !isset($_SERVER['HTTP_REFERER'])) {
	header('Location: ../?p=home');
}
else {
	header("Location: {$_SERVER['HTTP_REFERER']}");
}
?>