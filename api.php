<?php
class InstaCalendarAPI{
	public static function query($query, $accessToken = false, $refreshToken = false, $id = false){
		$options = insta_get_options();
		$key = $options['key'];
		$club = $options['club'];
		if($id){
			$key = isset($options['key_'.$id]) && $options['key_'.$id] ? $options['key_'.$id] : false;
			$club = isset($options['club_'.$id]) && $options['club_'.$id] ? $options['club_'.$id] : false;
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

		curl_close($ch);
		$data = json_decode($result);

		if($code == 401){
			$result = $data->errors[0]->result;

			// Если нужно обновить токен
			if($result == 5){
				$token = self::refreshToken($refreshToken);
				if($token){
					return self::query($query, $token->accessToken, $token->refreshToken, $id);
				}
			}
		}else if($accessToken && $refreshToken){
			$data->token = [
				'accessToken' => $accessToken,
				'refreshToken' => $refreshToken,
			];
		}
		$data->query = $query;
		$data->headers = $headers;

		if(isset($data->errors)){
			wp_send_json_error([
				'errors' =>$data->errors,
				'query' => $query,
				//'headers' => $headers,
				'code' => $code,
			]);
		}

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

		curl_close($ch);

		$data = json_decode($result);

		if(isset($data->data->tokenRefresh)){
			return $data->data->tokenRefresh->token;
		}else{
			return false;
		}
	}
}