<?php
include_once('library/common.php');
if($_GET['CDN_URL']){
	exit(cdn_url($_GET['CDN_URL']));
}
echo <<<EOF
var __cdnUrlMap = {};
function cdn_url(url){
	if(!__cdnUrlMap[url]){
		__cdnUrlMap[url] = $.ajax({
			url: "/group_site/jscdn.php?CDN_URL="+url,
			async: false
		}).responseText;
	}
	return __cdnUrlMap[url];
}
EOF;
?>