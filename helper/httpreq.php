<?php

/**
 * Simple HTTP request. Uses shared global $g_curlHandle to store CURL handle across requests.
 * Supports optional cookie jar. Does not validate SSL certificates. 
 * @param $url URL to request
 * @param $data POST data, either array or string. If false then request is assumed to be GET.
 * @param $cookiejar If cookies should be enabled pass writable file name here.
 * @return Result page. Use httpReqErr() to check for possible error. 
 */
function httpReq( $url, $data=false, $cookiejar=false, $timeout=10 )
{
	if ( $data && is_array($data) )
		$data = http_build_query($data,'i','&');
	
	global $g_curlHandle;
	if ( !isset($g_curlHandle) )
	{
		if ( !is_callable('curl_init') )
			throw new Exception( 'You need to install php5-curl first. On Ubuntu run apt-get install php5-curl' );
		$g_curlHandle = curl_init();
	}
	
	$ch = &$g_curlHandle;
	curl_setopt($ch, CURLOPT_URL, $url );
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 0);
	curl_setopt($ch, CURLOPT_COOKIESESSION, 0);
	curl_setopt($ch, CURLOPT_CRLF, 0);
	curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	curl_setopt($ch, CURLOPT_FILETIME, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
	curl_setopt($ch, CURLOPT_NETRC, 0);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);
	curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_UPLOAD, 0);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	curl_setopt($ch, CURLOPT_USERAGENT, 'mmvc/curl' );
	curl_setopt($ch, CURLOPT_PUT, 0);
	curl_setopt($ch, CURLOPT_HTTPGET, 0);
	curl_setopt($ch, CURLOPT_POST, 0);
	
	if ($data!==false)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
	}
	else
	{
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
	}
	if ($cookiejar)
	{
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar );
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar );
	}
	
	return curl_exec($ch);
}

/** 
 * Returns error string if occured in last httpReq(), or false if no errors. 
 */
function httpReqErr()
{
	global $g_curlHandle;
	if ( !isset($g_curlHandle) )
		return false;
	$err = curl_error( $g_curlHandle );
	return $err != '' ? $err : false;
}

?>