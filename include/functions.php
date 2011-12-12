<?php

define("DIR", "DIR");
define("LINK", "LINK");

function check_domain($site) {

	$domain=get_domains();
	
	foreach($domain as $key => $value) {
		if($value == $site){
			return true;
		}
	}
	return false;
	
}

function checkdir($path) {
	if (file_exists($path) && is_dir($path) && !is_link($path)) {
		return constant("DIR");
	} elseif (is_link($path)) {
		return constant("LINK");
	}	
	return false;
}

function get_domains() {

$Socket = new HTTPSocket;
$Socket->connect('127.0.0.1',2222);
$Socket->set_login($_SERVER['USER']);

$Socket->query('/CMD_API_SHOW_DOMAINS');
	$r_query_result = $Socket->fetch_parsed_body();
	if(is_array($r_query_result['list'])) {
		return $r_query_result['list']; 
	} else{
		return array();
	}
}
?>