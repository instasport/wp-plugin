<?php
class InstaCalendarAPI{
	public static function query($query, $accessToken = false, $refreshToken = false, $id = false){
		//sleep(30);
		$options = insta_get_options();
		$key = $options['key'];
		$club = $options['club'];
		if($id){
			$key = $options['key_'.$id] ?? '';
			$club = $options['club_'.$id] ?? '';
		}

		$headers = [
		'Content-Type: application/json',
		"Authorization: ". ( $accessToken ? "Token $accessToken" : "Key $key") . " $club",
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://instasport.co/api/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	//	var_dump(curl_getinfo($ch));

		curl_close($ch);
		$data = json_decode($result);

		if($code == 401){
			$result = $data->errors[0]->result;
			// Если нужно обновить токен
			if($result == 5){
				$token = self::refreshToken($refreshToken);
				if($token){
					return self::query($query, $token->accessToken, $token->refreshToken);
				}
			}else{
			//	var_dump($data);
			}
		}else if($accessToken && $refreshToken){
			$data->token = [
				'accessToken' => $accessToken,
				'refreshToken' => $refreshToken,
			];
		}
		$data->query = $query;
		$data->headers = $headers;
		//var_dump($query);
		return $data;
	}

	private static function refreshToken($refreshToken){
		$query = '{ "query": "mutation { tokenRefresh {token {accessToken refreshToken}}}" }';
		$headers = [
			'Content-Type: application/json',
			"Authorization: Refresh $refreshToken",
		];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://instasport.co/api/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		$data = json_decode($result);
		if(isset($data->data->tokenRefresh)){
			return $data->data->tokenRefresh->token;
		}else{
			return false;
		}
	}
}