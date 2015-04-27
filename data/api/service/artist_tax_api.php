<?php
require_once 'library/artist_tax.class.php';
$at = new ArtistTax();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']) {
	case 'GetArtistTax':
		echo json_encode($at->getArtistTax($json['Data']));
		break;
	case 'ProxyIntegrationDetail':
		echo json_encode($at->proxyIntegrationDetail($param['Uin'],$json['GroupId'],$json['Search']));
		break;
}
