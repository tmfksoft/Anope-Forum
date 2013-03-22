<?php
if (file_exists("../inc/config.php")) {
	//header('Location: ../');
	//die();
	// Already Installed!
}
?>
<form action="install.php" method="post">
	<h1>Install</h1>
	<hr></hr>
	<h3>Forum</h3>
	<b>Forum Title</b>
	<br/>
	<input name="title"/>
	<p/>
	<b>Forum Theme</b>
	<?php
	echo "<select name=\"theme\">";
	$themes = scandir("../templates");
	foreach ($themes as $thm) {
		if ($thm[0] != "." && $thm != "index.php") {
			echo "<option value=\"{$thm}\">{$thm}</option>";
		}
	}
	echo "</select>";
	?>
	<p/>
	<h3>MySQL / Existing Services</h3>
	Enter the details for your existing Anope Database:
</form>