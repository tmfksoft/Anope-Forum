<?php
include("../inc/core.php");
if ($_SESSION['anope_id'] == "" || !isset($_SESSION['anope_id']) || !get_username($_SESSION['anope_id'])) {
	//Not logged in else no such user.
	redir();
}
if ($_GET['brd'] == "" || !isset($_GET['brd'])) {
	redir();
}
if ($_POST['message'] == "" || !isset($_POST['message'])) {
	redir();
}
if ($_POST['title'] == "" || !isset($_POST['title'])) {
	redir();
}
if (!board_title(htmlentities($_GET['brd']))) {
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

$board = htmlentities($_GET['brd']);
$user = $_SESSION['anope_id'];
$title = mysql_real_escape_string($_POST['title']);
$message = htmlentities($bbcode->parse($_POST['message']));
$message = mysql_real_escape_string($message);

$prefix = $config['mysql_pref'];
$ts = time();

$query = "INSERT INTO {$prefix}ForumThreads (name,author,ts,board) VALUES('{$title}','{$user}','{$ts}','{$board}')";
$core->db_query($query,false);

$thds = thread_list($board);
$thread_count = count($thds);
$thread = $thds[$thread_count-1]['id'];

$query = "INSERT INTO {$prefix}ForumMessages (author,message,thread,ts) VALUES('{$user}','{$message}','{$thread}','{$ts}')";
$core->db_query($query,false);


redir('../?p=thread&id='.$thread);
?>