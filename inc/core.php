<?php
/*
 * Anope Forum CORE File!
 * No need to edit anything here!
 * Theme's have their own variable scope so you CANNOT collide with core variables or edit them.
 * This Core can be some what included in your own site so you may use functions :)
 */
$ver = "01"; // Please dont edit! :D
$config = array();
include("config.php");
$forum = array();

$forum['title'] = "Ilkotech Forum";
session_start();

// Extar Libs

// BBCode! Docs for this Library are here: http://www.christian-seiler.de/projekte/php/bbcode/doc/en/chapter1.php
// The Default theme contains some example Code for adding BBTags. We've added the common ones in the default theme core.
include("bbcode/stringparser_bbcode.class.php");
$bbcode = new StringParser_BBCode();

// Include the theme core.
$addr = "templates/".theme()."/".theme().".core";
if (file_exists($addr)) {
	include($addr);
}

class forum {
	private function init_db() {
		global $config;
		$res = mysql_connect($config['mysql_host'],$config['mysql_user'],$config['mysql_pass']) or die(mysql_error());
		mysql_select_db($config['mysql_data']);
		return $res;
	}
	public function db_query($query,$doret = true) {
		$res = $this->init_db();
		$return = mysql_query($query,$res) or die(mysql_error());

		// Returns a single string for 1 result and an array for multiple.
		if ($doret) {
			$result = array();
				while($row = mysql_fetch_assoc($return)) {
				$result[] = $row;
			}
			if ($result < 0) {
				return false;
			}
			else {
				return $result;
			}
		}
	}
	public function db_get($table,$item,$where = "") {
		global $config;
		$prefix = $config['mysql_pref'];
		return $this->db_query("SELECT {$item} FROM {$prefix}{$table} {$where}");
	}
	public function load_theme($name,$page,$args = false) {
		global $ver,$bbcode;
		if ($args) {
			extract($args);
		}
		$page = strtolower($page);
		$addr = "templates/{$name}/{$page}.tmp";
		include($addr);
	}
	public function page_exists($name,$page) {
		$page = strtolower($page);
		$addr = "templates/{$name}/{$page}.tmp";
		if (file_exists($addr)) {
			return true;
		} else {
			return false;
		}
	}
	public function do_log($message) {
		if (file_exists("error.forum.log")) {
			$prev = file_get_contents("error.forum.log")."\n";
		}
		else { $prev = ""; }
		$new = time().": ".$message;
		file_put_contents("error.forum.log",$prev.$new);
	}
	public function check_login($username,$password) {
		$username = htmlentities($username);
		$password = md5($password);
		$id = $this->db_get("NickCore","id","WHERE display='{$username}' AND pass='md5:{$password}'");
		if ($id > 0) {
			return $id[0]['id'];
		}
		else {
			return false;
		}
	}
}
$core = new forum;

// Functions for templates.
function message_list($thread) {
	global $core;
	$ret = $core->db_get("ForumMessages","*","WHERE thread='{$thread}'");
	return $ret;
}
function thread_list($board) {
	global $core;
	$ret = $core->db_get("ForumThreads","*","WHERE board='{$board}'");
	return $ret;
}
function board_list() {
	global $core;
	$ret = $core->db_get("ForumBoards","id");
	return $ret;
}
// BOARD STUFF
function board_title($board) {
	global $core;
	$ret = $core->db_get("ForumBoards","name","WHERE id='{$board}'");
	return $ret[0]['name'];
}
function thread_title($thread) {
	global $core;
	$ret = $core->db_get("ForumThreads","name","WHERE id='{$thread}'");
	return $ret[0]['name'];
}
function thread_info($thread) {
	global $core;
	$ret = $core->db_get("ForumThreads","*","WHERE id='{$thread}'");
	return $ret[0];
}
function board_info($board) {
	global $core;
	$ret = $core->db_get("ForumBoards","*","WHERE id='{$board}'");
	return $ret[0];
}
function user_info($uid) {
	global $core;
	$ret = $core->db_get("NickCore","*","WHERE id='{$uid}'");
	return $ret[0];
}
function forum_title() {
	global $config;
	echo $config['forum_title'];
}
function theme() {
	global $config;
	return $config['forum_theme'];
}
function is_logged_in() {
	if (isset($_SESSION['anope_id'])) {
		return true;
	}
	else {
		return false;
	}
}
// Vital note on this function. It's using 'anope_id'. You can use this to pretty much intergrate nicely.
// E.g. Use the forum login form for your site and have your site read anope_id
function current_user($item = false) {
	global $core;
	if ($item != false) { $item = strtolower($item); }
	if ($item = false) {
		// Return current ID.
		return $_SESSION['anope_id'];
	}
	else if ($item = "username") {
		get_username($_SESSION['anope_id']);
	}
	else {
		$core->do_log("Invalid Option {$item} for Function current_user!");
		return false;
	}
}
function get_username($id = false) {
	global $core;
	if ($id) {
		$nick = $core->db_get("NickCore","display","WHERE id='{$id}'");
		return $nick[0]['display'];
	}
	else {
		return get_username($_SESSION['anope_id']);
	}
}
function user_id() {
	if (isset($_SESSION['anope_id'])) {
		return $_SESSION['anope_id'];
	}
	else {
		return false;
	}
}
function url($address,$text,$alt = false) {
	$code = "<a href=\"{$address}\"";
	if ($alt) { $code .= " alt=\"{$alt}\""; }
	$code .= ">{$text}</a>";
	return $code;
}
function capitalise($text) {
	$text = strtolower($text); // Pull it down.
	$first = $text[0];
	$rest = substr($text, 1);
	return strtoupper($first).$rest;
}
// User Stuff
function is_suspended($id) {
	// See https://github.com/anope/anope/blob/1.9/include/account.h
	// For implementing your own functions in your custom themes.
	$data = user_info($id);
	$flags = explode(" ",$data['flags']);
	$ret = array_search("SUSPENDED", $flags);
	if ($ret) {
		return true;
	}
	else {
		return false;
	}
}
function is_oper($id = false) {
	global $core;
	if ($id) {
		$user = get_username($id);
		$ret = $core->db_get("Oper","type","WHERE name='{$user}'");
		if ($ret) {
			return $ret[0]['type'];
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
function suspend_user($id) {
	if (is_logged_in()) {
		if (is_oper(user_id()) && user_id() != $id) {
			// Let's suspend the user.
			$data = user_info($id);
			$flags = explode(" ",$data['flags']);
			$ret = array_search("SUSPENDED", $flags);
			if (!$ret) {
				// Not already suspended :D
				global $config,$core;
				$prefix = $config['mysql_pref'];
				$flags[] = "SUSPENDED";
				$flags = implode(" ",$flags);
				$ret = $core->db_query("UPDATE {$prefix}NickCore SET timestamp='".time()."', flags='{$flags}' WHERE id='{$id}'",false);
			}
			else {
				// Already suspended. Let's pretend we've done something.
				return true;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}
// Forms
function login_form() {
	// Adds a Forgot Password link if the Theme has the 'forgot' page.
	global $core;
	?>
	<form method="post" action="do/login.php">
		<table>
			<tr>
				<td>Username:</td>
				<td><input name="uname"/></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input name="upass" type="password"/></td>
			</tr>
			<tr>
				<td><?php if ($core->page_exists(theme(),"forgot")) { ?><a href="./?p=forgot">Forgot Password</a><?php } ?></td>
				<td><input type="submit" value="Login"/></td>
				<?php if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != "") { echo "<input type=\"hidden\" name=\"ref\" value=\"{$_SERVER['HTTP_REFERER']}\"/>"; } ?>
			</tr>
		</table>
	</form>
	<?
}
function reply_form($thread) {
	?>
	<form id="reply" action="do/reply.php?thd=<?php echo $thread; ?>" method="post">
	<textarea name="message" style="width:100%"></textarea>
	<br/>
	<input value="Reply" type="submit"/>
	</form>
	<?php
}
function thread_form($board) {
	?>
	<form id="reply" action="do/new_thread.php?brd=<?php echo $board; ?>" method="post">
	Thread Title:
	</br>
	<input name="title" size="50"/>
	<br/>
	Thread Message:
	<br/>
	<textarea rows="10" name="message" style="width:100%"></textarea>
	<br/>
	<input value="Make" type="submit"/>
	</form>
	<?php
}
?>