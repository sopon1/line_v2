<?php 

require "vendor/autoload.php";
include "admin/config.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = "o651NudRMzsU5jfijiPgTiFpo2pAslFXFKL7/c9bUmXp8TmsF7zOm3DQUsCH3ctE0JRODvO4NLFe5eUu6oj+XVbhLjkr3q8DQXLscXy+vLFXBpWFDY+Hg6Z1lMr6LBamBrkNl3RZQZ83H+EoofmRLwdB04t89/1O/w1cDnyilFU=";
$channelSecret = 'f8bfb388c9ea304291635116d0547425';
$content = file_get_contents('php://input');
$events = json_decode($content, true);

error_log($events['events']);

if (!is_null($events['events'])) {
	foreach ($events['events'] as $event) {
	
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			
			error_log($event['message']['text']);
			$text = $event['message']['text'];
			$replyToken = $event['replyToken'];
			## เปิดสำหรับใช้่งาน mysql message
			// $text = searchMessage($text ,$conn);
			$messages = setText($text);
			sentToLine( $replyToken , $access_token , $channelSecret , $messages );
		}
	}
}

function setText( $text){
	$messages = [
		'type' => 'text',
		'text' => $text
	];
	return $messages;
}

function searchMessage($text , $conn){
	$sql = "SELECT * FROM data where keyword='".$text."' ";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$message = $row['intent'];
		}
	} else {
		$message = "ไม่เข้าใจอ่ะ";
	}
	$conn->close();
	return $message;
}

function sentToLine($replyToken , $access_token , $channelSecret , $messages ){
	$url = 'https://api.line.me/v2/bot/message/reply';
	$data = [
		'replyToken' => $replyToken,
		'messages' => [$messages],
	];
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result . "\r\n";
}
