<?php // callback.php

require "vendor/autoload.php";
require_once('vendor/linecorp/line-bot-sdk/line-bot-sdk-tiny/LINEBotTiny.php');

$access_token = 'Y28YMb1IdjiF+FGoiz/1Tm5Tag04ILx1lBsSqWBnsZGy77CHjyePh6uq0vCh+iOn3ghxNa+20PWm/G5EC53MRYG2vHCBkJufsIcY7efjptxqCYieJLNQYyQOizN47rcIPUJSQZS+oxNfg86POdveEgdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['source']['userId'];
			$UserMessage = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];


			// Me 
			$commandText = array(
				'list' => array('sulg'=>'regislist','para'=>NULL),
				'sizem' => array('sulg'=>'regisSize','para'=>1),
				'sizexl' => array('sulg'=>'regisSize','para'=>2),
				'size2xl' => array('sulg'=>'regisSize','para'=>3),
			);
			$howtoUseText = '1. จำนวนผู้ลงทะเบียนทั้งหมด คำสั่ง list '."\r\n";
			$howtoUseText .= '2. จำนวน Size เสื้อ คำสั่ง sizem , sizexl , size2xl '."\r\n";
			if( in_array($UserMessage, $commandText) ){
				$command = $commandText[$UserMessage];
				$slug = $command['sulg'];
				$para = $command['para'];
				$responseData = postData($slug,$para);
				$textData = json_decode($responseData, $para);
				$text = $textData['msg'];
			}else{
				$text = $howtoUseText;
			}

			// echo $text;

			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text
			];

			// Make a POST Request to Messaging API to reply to sender
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
	}
}
// echo "OK naja";

echo postData();


function postData($slug,$para=NULL){
	$url = 'http://siamparagon.co.th/WeDoGoodWithHeart/Linebot/'.$slug;
	$myvars = 'para='.$para;

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec( $ch );
	return $response;
}
