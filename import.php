<?php

include 'config.php';
require_once 'vendor/autoload.php';

// Connect to database, or create it if it doesn't exist
$db = new PDO('sqlite:'.SQLLITE_DB_FILENAME);

// Add tables if the database is empty
if (filesize(SQLLITE_DB_FILENAME) == 0) {
	if ($db->exec("CREATE TABLE repos (id INTEGER PRIMARY KEY, name TEXT, fullname TEXT, html_url TEXT)") !== false) echo "[repos] table created.\n";
	if ($db->exec("CREATE TABLE weekly_commits (repo_id INTEGER, week TIMESTAMP, additions INTEGER, deletions INTEGER, commits INTEGER, PRIMARY KEY(repo_id, week))")!== false) echo "[weekly_commits] table created.\n"; 
}

$client = new \Github\Client(
    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
);

$client->authenticate(GITHUB_PERSONAL_ACCESS_TOKEN_USER, GITHUB_PERSONAL_ACCESS_TOKEN_PASSWORD, Github\Client::AUTH_HTTP_PASSWORD);

echo "......................................................\n";
echo "Fetching repositories.................................\n";
echo "......................................................\n";

$repos = $client->api('repo')->org('fubralimited');

foreach ($repos AS $repo) {
	
	echo $repo['name'] ."\n";
	
	$stmt = $db->prepare('INSERT OR IGNORE INTO repos 
		(id, 
		name, 
		fullname, 
		html_url) 
		VALUES (?, ?, ?, ?) ');
		
	$query = $stmt->execute(array(
		$repo['id'], 
		$repo['name'], 
		$repo['fullname'],
		$repo['html_url']
	));
	
	if (!$query) {
    	die(print_r($db->errorInfo()) );
    }
}

echo "......................................................\n";
echo "Fetching weekly commits...............................\n";
echo "......................................................\n";

$repos = $client->api('repo')->org('fubralimited');



?>