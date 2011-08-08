<?php
$name = AppletInstance::getValue('name');
$hash = AppletInstance::getValue('hash');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://api.twitter.com/1/statuses/user_timeline.json?&include_entities=1&screen_name=' .$name);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$tweets = json_decode(curl_exec($ch));
curl_close($ch);

foreach ($tweets as $tweet) {
	foreach($tweet->entities->hashtags as $hashtags) {
		if($hashtags->text == $hash) {
			$OneTweet = $tweet;
			$OneTweet = str_replace("#" .$hash, "", $OneTweet->text);
			break 2;
		}
	}
}

if(!isset($OneTweet)) {
	$OneTweet = $tweets[0]->text;
}


$response = new Response();

if(AppletInstance::getFlowType() == 'voice'){
	$response->addSay(trim($OneTweet));
	$next = AppletInstance::getDropZoneUrl('next');
	if(!empty($next))
		$response->addRedirect($next);
}
else
	$response->addSms($OneTweet);

$response->Respond();