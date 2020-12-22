<?php
class InstaCalendarAjax {

	public function __construct() {


		add_action('wp_ajax_insta_api', [$this, 'insta_api_callback']);
		add_action('wp_ajax_nopriv_insta_api', [$this, 'insta_api_callback']);
	}

	function insta_api_callback(){
		$query = $_POST['query'] ?? '';
		$accessToken = $_POST['accessToken'] ?? false;
		$refreshToken = $_POST['refreshToken'] ?? false;
		if($query){
			if($data = InstaCalendarAPI::query('{ "query": "'.$query.'"}', $accessToken, $refreshToken)){

				echo wp_json_encode($data);
			}
		}
		exit;
	}
}

new InstaCalendarAjax();