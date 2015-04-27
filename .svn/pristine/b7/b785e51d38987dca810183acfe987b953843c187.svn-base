<?php
require_once 'library/artist.class.php';
$artist = new Artist();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetArtistSalary':
		echo json_encode($artist->getArtistSalary($json['Uin'], $json['RoomId'], $json['GroupId']));
		break;
	case 'EditArtistSalary':
		echo json_encode($artist->editArtistSalary($json['Salary'], $json['Uin'], $json['RoomId'], $json['GroupId']));
		break;
}