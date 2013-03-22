<?php
include("../inc/core.php");
if ($_SESSION['anope_id'] == "" || !isset($_SESSION['anope_id']) || !get_username($_SESSION['anope_id'])) {
	//Not logged in else no such user.
	redir();
}
if ($_GET['thd'] == "" || !isset($_GET['thd'])) {
	redir();
}
if ($_POST['message'] == "" || !isset($_POST['message'])) {
	redir();
}
if (!thread_title(htmlentities($_GET['thd']))) {
	redir();
}
function redir($url = false) {
	if (!$url) {
		header('Location: ../');
	}
	else {
		header('Location: '.$url);
	}
	die();
}

$thread = htmlentities($_GET['thd']);
$user = $_SESSION['anope_id'];
$message = htmlentities($bbcode->parse($_POST['message']));
$message = mysql_real_escape_string($message);

$prefix = $config['mysql_pref'];
$ts = time();

$query = "INSERT INTO {$prefix}ForumMessages (author,message,thread,ts) VALUES('{$user}','{$message}','{$thread}','{$ts}')";

$core->db_query($query,false);

redir('../?p=thread&id='.$thread);
?>