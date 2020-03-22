<!DOCTYPE html>
<html>

<head>
<title>read/grep</title>
</head>

<body>

<h3>read/grep</h3>

<?php
/*$myfile=fopen("test", "r");
while(!feof($myfile)){
	echo fgets($myfile) . "<br>";
}
fclose($myfile)*/

$output = shell_exec("grep 'e' test");
echo "<pre>$output</pre>";
?>

</body>
</html>
