<?php
/**
 * Premier Validate Client Library
 *
 * @copyright  Copyright (c) 2013-2016, Prema Solutions.
 * @author     Brian Tafoya
 * @version    1.1
 *
 * USE:
 * $premierValidateClient = new premierValidateClient("{your-api-key}");
 * $results = $premierValidateClient->validateEmail("user@recdomain.com");
 */

class premierValidate {

	private $api_key;
	private $base_url = "https://api.premiervalidate.com/";
	public $last_connection_details = array();
	public $last_connection_data = NULL;
	public $last_connection_error = NULL;
	
	public function __construct($api_key) {
		$this->api_key = $api_key;
	}
	
	public function validateEmail($email) {
		return $this->doRequest($this->base_url . "ws/v1/validateEmail/json", array("api_key"=>$this->api_key,"email"=>$email));
	}
	
	public function sendMessage($email_to, $email_to_name, $email_subject, $email_body, $email_body_type, $email_from, $callback_url = "") {
		return $this->doRequest($this->base_url . "ws/v1/sendMessage", array(
			"api_key"=>$this->api_key,
			"email_to"=>$email_to,
			"email_to_name"=>$email_to_name,
			"email_subject"=>$email_subject,
			"email_body"=>$email_body,
			"email_body_type"=>$email_body_type,
			"email_from"=>$email_from,
			"callback_url"=>$callback_url
		));
	}

	public function isHamEmail($email) {
		return $this->doRequest($this->base_url . "ws/v1/isHamEmail/json", array("api_key"=>$this->api_key,"email_from"=>$email));
	}
	
	public function isSpamEmail($email) {
		return $this->doRequest($this->base_url . "ws/v1/isSpamEmail/json", array("api_key"=>$this->api_key,"email"=>$email));
	}

	private function doRequest($url, array $post = NULL, array $options = array()) {
		$defaults = array(
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_USERAGENT => "PremierValidateAPI",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_VERBOSE => 0,
			CURLOPT_FAILONERROR => 1,
			CURLOPT_TIMEOUT => 90,
			CURLOPT_COOKIEJAR => 'cookie.txt',
			CURLOPT_COOKIEFILE => 'cookie.txt',
			CURLOPT_POSTFIELDS => http_build_query($post)
		);

		$ch = curl_init();
		curl_setopt_array($ch, ($options + $defaults));
		$this->last_connection_data = curl_exec($ch);
		$this->last_connection_error = array("curl_errno" => curl_errno($ch), "curl_error" => curl_error($ch));

		if (!$this->last_connection_error) {
			$this->last_connection_details = curl_getinfo($ch);
			curl_close($ch);
			if ($this->last_connection_data) {
				return json_decode($this->last_connection_data);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
