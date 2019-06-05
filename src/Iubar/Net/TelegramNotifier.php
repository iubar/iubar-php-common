<?php
namespace Iubar\Net;

// use GuzzleHttp\Client;

class TelegramNotifier {

	public static function notify($title, $message) {
		$config_file = realpath(__DIR__) . '/../../../config/config.ini';
		if (!is_file($config_file)) {
			die("The config file is missing: $config_file" . PHP_EOL);
		}
		$config = parse_ini_file($config_file);		
		$token = $config['telegram.token'];
		$chat_id = $config['telegram.chatid'];
		
		$base_uri = 'https://api.telegram.org';
		$message = '<b>' . $title . '</b>' . PHP_EOL . $message;
		$get_req = $base_uri . '/bot' . $token . '/sendMessage?chat_id=' . $chat_id . '&parse_mode=HTML&text=' . urlencode($message);
		$response = file_get_contents($get_req);
		// oppure
		//
		// $client = new Client([
		// 'base_uri' => $base_uri . '/',
		// 'timeout' => 3.0,
		// ]);		
		// $response = $client->request('GET', $get_req);
		return $response;
	}
}