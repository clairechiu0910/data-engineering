<!DOCTYPE html>
<html>

<head>
<meta http-equiv = "Content-Type" content = "text/html; charset = UTF-8" />
<title>search result</title>
<style>
.center{
	width: 80%;
	position: absolute;
	left: 50px;
}
</style>
</head>

<body>
<div class="center">
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

	$outStr = shell_exec("./rgrep -p $search");
	$outArr = explode("\n", $outStr);
	$dataNum = sizeof($outArr);
	$pageSum = ceil($dataNum/30);
	//print_r($outArr);

	echo "<pre>Search: $search 共 $dataNum 筆結果</pre>";
	echo "<pre>Result: </pre>";

	for($i = ($nowPage-1)*30; $i<($nowPage*30); $i = $i+1){
		if($i+($nowPage-1)*30 >= $dataNum) break;
		$tmp = $outArr[$i];
		$posTopic = strpos($tmp, "@T:");
		$posWeb = strpos($tmp, "@W:");
		$posCont = strpos($tmp, "@C:");

		$tmpScore = substr($tmp, 3, ($posTopic-3));
		$tmpTopic = substr($tmp, $posTopic+3, ($posWeb-$posTopic-3));
		$tmpWeb = substr($tmp, $posWeb+3, ($posCont-$posWeb-3));
		$tmpCont = substr($tmp, $posCont+3, 800);

		echo "<h3><a href=$tmpWeb>$tmpTopic</a></h3>";
		echo "<h4>$tmpWeb</h4>";
		echo "<h4 align='right'>score:$tmpScore</h4>";
		echo "<h4>$tmpCont ...</h4>";
		//echo "<h4 style='width:1500px' align='right'>score:$tmpScore</h4>";
		//echo "<h4 style='width:1500px'>$tmpCont ...</h4>";
	}
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
