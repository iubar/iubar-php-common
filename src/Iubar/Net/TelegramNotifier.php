<?php
namespace Iubar\Net;

// use GuzzleHttp\Client;

class TelegramNotifier {

	public static function notify($message) {
		$config_file = realpath(__DIR__) . '/../../../../config/build.properties';
		if (!is_file($config_file)) {
			die("Missing $config_file" . PHP_EOL);
		}
		$config = parse_ini_file($config_file);
		
		$token = $config['telegram.token'];
		$chat_id = $config['telegram.chatid'];
		$app_name = "IUBAR BUILDER";
		
		$message = '<b>' . $app_name . '</b>' . PHP_EOL . $message;
		$get_req = '/bot' . $token . '/sendMessage?chat_id=' . $chat_id . '&parse_mode=HTML&text=' . urlencode($message);
		$response = file_get_contents($get_req);
		// oppure
		//
		// $client = new Client([
		// 'base_uri' => 'https://api.telegram.org/',
		// 'timeout' => 3.0,
		// ]);		
		// $response = $client->request('GET', $get_req);
		return $response;
	}
}