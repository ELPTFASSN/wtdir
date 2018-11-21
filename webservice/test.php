<?php
$server='localhost';
$username='root';
$password='@i3cFg#7mP!2t?';
$database='misdir_wtidir';

$mysqli = new mysqli($server,$username,$password);
$stmt = $mysqli->stmt_init();
$query = 'select catalog_name from information_schema.schemata';

$result = array();
if($stmt->prepare($query)){
	$stmt->execute();
	$result = get_result($stmt);
} else{
	$result[] = 'fail';
}

die(json_encode($result));

function get_result($stmt){
	$meta = $stmt->result_metadata();
	while($field = $meta->fetch_field()){
		$parameters[] = &$row[$field->name];
	}
	call_user_func_array(array($stmt, 'bind_result'), $parameters);
	$result = array();
	while ($stmt->fetch()) {
		foreach($row as $key => $val) {
			$data[$key] = $val;
		}
		$result[] = $data;
	}
	return $result;
}

?>