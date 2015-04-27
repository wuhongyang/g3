<?php
function paiallel_request($url,$num=1){
	if($num <= 0) $num = 1;
	$handle = array();
	$mh = curl_multi_init();
	while($num--){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_multi_add_handle($mh, $ch);
		$handle[] = $ch;
	}
	$running=null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	$response = curl_multi_getcontent($ch);
	foreach($handle as $chs){
		curl_multi_remove_handle($mh, $chs);
	}
	curl_multi_close($mh);
	unset($handle);
	return $response;
}
