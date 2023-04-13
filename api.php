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
			'Content-Type' => 'application/json',
			'Authorization' =>  ( $accessToken ? "Token $accessToken" : "Key $key") . " $club",
		];

		$options = insta_get_options();
		$response = wp_remote_post( $options['api_url'], [
			'body' => $query,
			'headers' => $headers,
			'timeout'     => 60,
		] );

		$data = json_decode($response['body']);

		$code = $response['response']['code'];

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
			'Content-Type' => 'application/json',
			'Authorization' => "Refresh $refreshToken",
		];

		$options = insta_get_options();
		$response = wp_remote_post( $options['api_url'], [
			'body' => $query,
			'headers' => $headers,
			'timeout'     => 60,
		] );

		$data = json_decode($response['body']);

		if(isset($data->data->tokenRefresh)){
			return $data->data->tokenRefresh->token;
		}else{
			return false;
		}
	}
}