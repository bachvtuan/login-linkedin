<?php
/*
  Reference:
	https://developer.linkedin.com/documents/sign-linkedin
	http://developer.linkedin.com/apis
	https://www.linkedin.com/secure/developer
	https://developer.linkedin.com/documents/authentication
 */
require_once ( 'httpreq.php' );

/**
 * Wrapper for LinkedIn API to simplify integration.
 * @ingroup auth_helpers
 */
class LinkedIn {

	private $apiKey;
	private $secretKey;
	private $state;	
	private $callbackLink;
	private $profileUrl = "http://www.linkedin.com/profile/view?id=%s";
	private $getTokenUrl = "https://www.linkedin.com/uas/oauth2/accessToken?grant_type=authorization_code&code=%s&redirect_uri=%s&client_id=%s&client_secret=%s";
	private $getUserInfoUrl = "https://api.linkedin.com/v1/people/~:(id,first-name,last-name,headline,email-address,picture-url)?oauth2_access_token=%s";
	
	/**
	 * LinkedIn login url 
	 */
	public $loginUrl;

	/**
	 * Constructor for helper
	 * @param string $api_key : api key for helper
	 * @param string $secet_key : secret key for helper
	 * @param string $call_back_link : callback link, request when login in Linkedin done.
	 */
	public function __construct($api_key, $secet_key, $call_back_link )
	{
		$this->apiKey 		= $api_key;
		$this->secretKey 	= $secet_key;
		$this->callbackLink = $call_back_link;

		$this->state = md5( $_SERVER['SERVER_ADDR'] );

		//Get full_profile, email and network
		$this->loginUrl = "https://www.linkedin.com/uas/oauth2/authorization?response_type=code".
				"&scope=r_fullprofile%20r_emailaddress%20r_network".
				"&client_id=".$this->apiKey."&state=$this->state"."&redirect_uri=". urlencode($this->callbackLink);
	}

	/**
	 * Checks authentication .
	 * @return Array with key status, info keys
	 */
	public function authentication()
	{
		$res = array('status' => 'error', 'info' => '');
			
		if (isset($_GET['error']))
		{
		 $res['info'] =  $_GET['error_description'];
		 return $res;
		}
		else
		{
			//Check code and state in GET params
			if ( !isset( $_GET['code'] ) && !isset($_GET['state']) )
			{
				$res['info'] = "Missing code or state in params";
				return $res;
			}

				
			$code = $_GET['code'];
			$state = $_GET['state'];
				
			if ( $state != $this->state )
			{
				$res['info'] = "State doesn't match";
				return $res;
			}
			
			/*
			* Get access token by code is gotten
			* Return json format e.g {"expires_in":5184000,"access_token":"AQXdSP_W41_UPs5ioT_t8HESyODB4FqbkJ8LrV_5mff4gPODzOYR"},
			* otherwise, given error for some reason
			*/
							
			$content =  httpReq( sprintf($this->getTokenUrl, $code, urlencode($this->callbackLink),$this->apiKey, $this->secretKey)  );
			$obj = json_decode($content);
			
			if  ( isset( $obj->expires_in ) && isset( $obj->access_token ) )
			{
				//Set status is ok	
				$res['status'] = 'ok';
			}
			
			$res['info'] = $obj;
			
			return $res;
		}
	}
	
	/**
	 * Returns user info.
	 * @param strig $token_id User token
	 * @return array : all neccessary information ( email, fullname,etc )
	 */
	public function getUserInfo( $token_id  )
	{
		$xml = httpReq( sprintf($this->getUserInfoUrl, $token_id) );
		
		/* convert object to array with key with id,first-name,last-name,headline,email-address */
		return json_decode(json_encode((array) simplexml_load_string( $xml ) ), 1);
		
	}
	
	/**
	 * Get profile link link by user_id
	 * @param string $user_id
	 * @return string 
	 */
	public function getProfileUrl( $user_id )
	{
		return sprintf($this->profileUrl,$user_id);
	}

}