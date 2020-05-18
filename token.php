<?php
if (isset($_GET['setwebhook'])) {
	if(!isset($_SERVER['DOCUMENT_URI'])) $_SERVER['DOCUMENT_URI'] = $_SERVER['SCRIPT_URL'];
	if (!function_exists('curl_init')) die('<h1>ERROR:</h1> <h3>请检查</h3>');
	if (!isset($_GET['token'])) die('<h1>ERROR:</h1> <h3>token没有</h3>');
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL            => "https://api.telegram.org/bot".$_GET['token']."/setWebhook",
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => ['url' => 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['DOCUMENT_URI'].'?api='.$_GET['token']],
		CURLOPT_RETURNTRANSFER => true,
CURLOPT_TIMEOUT        => 10
	]);

	$response = curl_exec($curl);
	$response = json_decode($response,true);
	if ($response['ok']) {
		die('<h1>成功啦，去电报回复任意数字看看</h1>');
	} elseif (curl_errno($curl) == '28') {
		die('<h1>ERROR:</h1> <h3>api错误，请看官方api</h3>');
	} else {
		die('<h1>ERROR:</h1> <h3>'.json_encode($response).'</h3><br/><a href="https://github.com/tcphp/telegram-bot">获得帮忙</a>');
	}	
	curl_close($curl);
}
