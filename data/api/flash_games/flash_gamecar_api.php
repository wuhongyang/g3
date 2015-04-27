<?php
require_once 'library/flash_gamecar.class.php';
$gamecar = new Flash_gamecar();

$action = $_POST['extparam']['Tag'];
$result = $gamecar->$action();

if( ! empty($gamecar->logdata) && $result['Flag'] == 100){
	$result['LogData'] = $gamecar->logdata;
}

echo json_encode($result);
