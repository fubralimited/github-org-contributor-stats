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

function display_commits ($output_type, $author_repo_commits, $author_total_commits) {
  
  if ($author_repo_commits > 0) {
    if ($output_type == 'percentage') {
      return number_format($author_repo_commits / $author_total_commits * 100, 0) . " %";
    } else {
      return $author_repo_commits;
    }
  } else {
      return '&nbsp;';
  } 
  
}

?>