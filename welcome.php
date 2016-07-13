<?php
if(!isset($argv[1])){
	usage();
}
$action = $argv[1];	
$term = "";
$user = "";
$response = "test response1";
$retweet = "false";
$follow = "false";
//$post = "false";

require 'bot.php';
$conn = new TwitterAutoResponder();

if($action == "feed"){
	if(!isset($argv[2])) usage();
	$feed_name = $argv[2];
	//read the feeds and tweet them
	echo "given arg1 is :".$action."\n";
}
elseif($action == "sr"){
	if(!isset($argv[2]) || !isset($argv[3])) usage();
	$term =  $argv[2];
	$response = $argv[3];
	echo "given arg1 is :".$action."\n";
	$conn->run($term,"",$response,"","");
}
elseif($action == "srt"){
	if(!isset($argv[2])) usage();
	$term =  $argv[2];
	$retweet = "true";
	echo "given arg1 is :".$action."\n";
	$conn->run($term,"","",$retweet,"");
}
elseif($action == "ur"){
	if(!isset($argv[2]) || !isset($argv[3])) usage();
	$user = $argv[2];
	$response = $argv[3];
	echo "given arg1 is :".$action."\n";
	$conn->run("",$user,$response,"","");
}
elseif($action == "urt"){
	if(!isset($argv[2])) usage();
	 $user = $argv[2];
	 $retweet = "true";
	 echo "given arg1 is :".$action."\n";
	 $conn->run("",$user,"",$retweet,"");
}elseif($action == "uf"){
	if(!isset($argv[2])) usage();
	$user = $argv[2];
	$follow ="true";
	echo "given arg1 is :".$action."\n";
	$conn->run("",$user,"","",$follow);
}
elseif($action == "sf"){
	if(!isset($argv[2])) usage();
	$term =  $argv[2];
	$follow ="true";
	echo "given arg1 is :".$action."\n";
	$conn->run($term,"","","",$follow);
}else{
	echo "posting a tweet from this bot";
	//$post = "true";
	$conn->test($response);
}

function usage(){
	echo "Invalid usage";
	echo "\n";
	echo "Usage is : \n";
	echo "php index.php [feed|sr|srt|ur|urt|uf|sf] (search_term|user)";
	echo "\n";
	die();
}
?>
