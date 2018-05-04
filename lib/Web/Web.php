<?php
abstract class Web {
	/**
	 * Check if a website is online
	 *
	 * @param      string  $site   Website adress
	 *
	 * @return     bool  online or not
	 */
	static function siteStatus($site)
	{
		$fp = @fsockopen($site, 80, $errno, $errstr, 1);

		return $fp;
	}

	/**
	 * Check if a server is online
	 *
	 * @param      <type>  $server  server adress
	 * @param      <type>  $port    server port
	 *
	 * @return     bool    online or not
	 */
	static function serverStatus($server,$port)
	{
		$fp = @fsockopen($server,$port, $errno, $errstr, 1);

		$online=($fp >= 1)?true:false;
		
		return $online;
	}

	/**
	 * Gets average ping
	 *
	 * @return     integer  average ping
	 */
	static function averagePing() {
		$hosts = array('google.com', 'wikipedia.org','twitter.com');
		
		$pings = array();
		$aping = 0;
		$i = 0;

		foreach ($hosts as $host) {
	   		exec('ping -qc 1 '.$host, $ping);
	   		$exploded = explode("=",$ping[3]);
	   		$exploded = explode("/",$exploded[1]);
			$aping=$aping+intval($exploded[1]);
			$i++;
		}	
		$aping = ceil($aping/$i);
		
		return $aping;
	}

	/**
	 * Get local and wan ip
	 *
	 * @return     array  data
	 */
	static function ipConfig()
	{
		$ipa = array();
	    $ipa['lan']= $_SERVER['SERVER_ADDR'];
	    $ipa['wan']= exec('curl http://ipecho.net/plain; echo');
		
		return $ipa;
	}

	/**
	 * Gets the client ip.
	 *
	 * @return     string  The client ip.
	 */
	static function get_client_ip() {
		$ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	/**
	 * Get the current full URL
	 *
	 * @param      <type>   $s                   { parameter_description }
	 * @param      boolean  $use_forwarded_host  The use forwarded host
	 *
	 * @return     string   ( description_of_the_return_value )
	 */
	static function full_url($s, $use_forwarded_host = false) {
	    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
	    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
	    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
	    $port     = $s['SERVER_PORT'];
	    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
	    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
	    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
	    return $protocol . '://' . $host . $s['REQUEST_URI'];
	}
}