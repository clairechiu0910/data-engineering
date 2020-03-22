<!DOCTYPE html>
<html>
<head>
<meta http-equiv = "Content-Type" content = "text/html; charset = UTF-8" />
<title>search result</title>
<style>
table, th, td{
	border: 1px solid black;
	text-align:left;
}
table{
	width: 100px;
}

</style>
</head>

<body>
<?php
	session_start();
	if(!empty($_POST["search"])){
		$search = $_POST["search"];
		$_SESSION["tmpSearch"] = $search;
	}
	else if(isset($_SESSION["tmpSearch"])){
		$search = $_SESSION["tmpSearch"];
	}

	$nowPage = 1;
	if(!empty($_POST["page"]) && $_POST["page"]>0){
		$nowPage = intval($_POST["page"]);
	}

	$outStr = shell_exec("grep $search sorted.rec");
	$outArr = explode("\n", $outStr);
	$dataNum = sizeof($outArr);
	$pageSum = ceil($dataNum/30);

	//read topic
	$fTop = fopen('./topic.rec', "r");
	$topArr = array("topic");
	if($fTop){
		while(!feof($fTop)){
			$tmp = fgets($fTop, 100000);
			array_push($topArr, $tmp);				
		}
	}
	else{
		echo "<pre>Read topic.rec failed.</pre>";
	}
	fclose($fTop);

/*	//read website
	$fWeb = fopen('./website.rec', "r");
	$webArr = array("website");
	if($fWeb){
		while(!feof($fWeb)){
			$tmp = fgets($fWeb, 100000);
			array_push($webArr, $tmp);
		}
	}
	else{
		echo "<pre>Read website.rec failed.</pre>";
	}
	fclose($fWeb);
	print_r($webArr);
*/
	echo "<pre>Result Table: </pre>";
	echo "<pre>Sizeof Array: $dataNum</pre>";
?>
<div width = "300">
	<table>
	<tr>
		<th>index</th>
		<th>content</th>
	</tr>
<?php
	for($i = ($nowPage-1)*30; $i<($nowPage*30); $i = $i+1){
		$topNum = (int)substr($outArr[$i], -10);
		$topNum = $topNum;
		$output = substr($outArr[$i], 0, -10);
?>
	<tr>
		<td><?php echo "<pre>$topNum  $topArr[$topNum]</pre>"; ?></td>
		<td><?php echo "<pre>$output</pre>"; ?></td>
	</tr>
<?php
	}
?>
	</table>
<?php
	echo "<pre>page $nowPage of $pageSum</pre>";
?>
<form method = "post" action = "search.php">
	Jump to: <input type = "intval" name = "page">
	<input type = "submit" value="Enter">
</form>
<form method = "post" action = "search.php">
	Search: <input type = "text" name = "search">
	<input type = "submit" value = "Enter">
</form>
</div>

</body>
</html>
