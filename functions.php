<?php
	
// Functions

function db_insert($db="", $table="", $data="") {	

	$sql = 'INSERT OR IGNORE INTO ' . $table . ' (' . implode(',',array_keys($data)) .') VALUES (' . implode(',' ,array_fill(0, count($data), '?')) . ')';
	
	$stmt = $db->prepare($sql);

	$query = $stmt->execute(array_values($data));

	if (!$query) {
		die(print_r($db->errorInfo()) );
	}

}

?>