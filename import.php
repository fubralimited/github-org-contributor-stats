<?php

include 'config.php';
require_once 'vendor/autoload.php';

// Delete existing database if REFRESH_DATABASE set
if (is_file(SQLLITE_DB_FILENAME) && REFRESH_DATABASE) {
	echo "Removing existing database ".SQLLITE_DB_FILENAME."\n";
	unlink (SQLLITE_DB_FILENAME);
}

// Connect to database, or create it if it doesn't exist
$db = new PDO('sqlite:'.SQLLITE_DB_FILENAME);

// Add tables if the database is empty
if (filesize(SQLLITE_DB_FILENAME) == 0) {
	if ($db->exec("CREATE TABLE repos (id INTEGER PRIMARY KEY, name TEXT, fullname TEXT, html_url TEXT)") !== false) echo "[repos] table created.\n";
	if ($db->exec("CREATE TABLE authors (id INTEGER PRIMARY KEY, login TEXT, gravatar_url TEXT, html_url TEXT)") !== false) echo "[authors] table created.\n";
	if ($db->exec("CREATE TABLE weekly_commits (repo_id INTEGER, author_id INTEGER, week TIMESTAMP, additions INTEGER, deletions INTEGER, commits INTEGER, PRIMARY KEY(repo_id, author_id, week))")!== false) echo "[weekly_commits] table created.\n"; 
}

$client = new \Github\Client(
    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
);

$client->authenticate(GITHUB_PERSONAL_ACCESS_TOKEN_USER, GITHUB_PERSONAL_ACCESS_TOKEN_PASSWORD, Github\Client::AUTH_HTTP_PASSWORD);

echo "......................................................\n";
echo "Fetching repositories.................................\n";
echo "......................................................\n";

// Relevant documentation:
// https://developer.github.com/v3/repos/#list-organization-repositories
// https://github.com/KnpLabs/php-github-api/blob/master/lib/Github/Api/Repo.php
// https://github.com/KnpLabs/php-github-api/blob/master/doc/result_pager.md

$organizationApi = $client->api('organization');
$paginator  = new \Github\ResultPager($client);
$parameters = array(ORGANISATION);
$repos      = $paginator->fetchAll($organizationApi, 'repositories', $parameters);

// Insert repos

$stmt = $db->prepare('INSERT OR IGNORE INTO repos 
	(id, 
	name, 
	fullname, 
	html_url) 
	VALUES (?, ?, ?, ?) ');

foreach ($repos AS $repo) {
	
	echo $repo['name'] ."\n";
	
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

foreach($repos AS $repo) {

	$repo_authors = $client->api('repo')->statistics(ORGANISATION, $repo['name']);

	echo "******* ".$repo['name'] ."[".count($repo_authors)." authors] *******\n";

	foreach ($repo_authors AS $author_data) {
		// print_r($author_data);

		echo $author_data['author']['login'] . " [" . $author_data['total'] . "]\n";

		// Insert author
				
		$stmt = $db->prepare('INSERT OR IGNORE INTO authors 
			(id, 
			login, 
			gravatar_url, 
			html_url) 
			VALUES (?, ?, ?, ?) ');

		$query = $stmt->execute(array(
			$author_data['author']['id'],
			$author_data['author']['login'],
			$author_data['author']['gravatar_url'],
			$author_data['author']['html_url']
		));

		if (!$query) {
			die(print_r($db->errorInfo()) );
		}
		// Insert weekly commit information
		foreach ($author_data['weeks'] AS $weekly_commits)  {
			
			// print_r($weekly_commits); exit();	
			if ($weekly_commits['c'] > 0) {
			
				$stmt = $db->prepare('INSERT OR IGNORE INTO weekly_commits 
					(repo_id, 
					author_id, 
					week,
					additions, 
					deletions,
					commits) 
					VALUES (?, ?, ?, ?, ?, ?)');

				$query = $stmt->execute(array(
					$repo['id'],	
					$author_data['author']['id'],
					$weekly_commits['w'],
					$weekly_commits['a'],
					$weekly_commits['d'],
					$weekly_commits['c']
				));

				if (!$query) {
					die(print_r($db->errorInfo()) );
				}
			
			}
		}
	}
} 


?>
