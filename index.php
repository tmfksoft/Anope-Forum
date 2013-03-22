<?php
$args = array();
include("inc/core.php");
if (isset($_GET['p'])) {
	$page = htmlentities($_GET['p']);
}
else {
	$page = "home";
}

if (isset($_GET['id']) && $_GET['id'] != "") {
	$args['id'] = htmlentities($_GET['id']);
}
else {
	$args['id'] = false;
}
$get = array();
foreach ($_GET as $key => $val) {
	if ($key != "id" && $key != "p") {
		$get[$key] = htmlentities($val);
	}
}
$args['HTTP_GET'] = $get;
$args['cpage'] = $page;
$core->load_theme(theme(),"header",$args);

$exists = $core->page_exists(theme(),$page);
if ($exists) {
	unset($exists);
	$core->load_theme(theme(),$page,$args);
}
else {
	$core->load_theme(theme(),"404",$args);
}

$core->load_theme(theme(),"footer",$args);
?>