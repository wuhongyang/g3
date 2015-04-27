<?php
include_once 'library/songs.class.php';
$param = $_POST['param'];
$json = $_POST['extparam'];
$song = new RoomSongs($param);

switch($json['Tag']){
	case 'AddSong':
		echo json_encode($song->addSong($json['Name'], $json['Author'], $json['AddOnly']));
		break;
	case 'EditSong':
		echo json_encode($song->editSong($json['OldName'], $json['Name'], $json['Author']));
		break;
	case 'DelSong':
		echo json_encode($song->delSong($json['Name'], $json['PageNum']));
		break;
	case 'PickSong':
		echo json_encode($song->pickSong());
		break;
}