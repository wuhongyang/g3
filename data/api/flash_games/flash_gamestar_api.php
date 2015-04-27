<?php
require_once 'library/flash_gamestar.class.php';
$game = new Flash_gamestar();

$action = $_POST['extparam']['Tag'];
$result = $game->$action();

if( ! empty($game->logdata) && $result['Flag'] == 100){
	$result['LogData'] = $game->logdata;
}

echo json_encode($result);
