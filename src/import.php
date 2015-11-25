<?php

require_once 'includes.php';

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

foreach ($repos AS $repo) {
	
	echo $repo['name'] ."\n";
	
	db_insert ($db, 'repos', array(
		'id' => $repo['id'],
		'name' => $repo['name'],
		'fullname' => $repo['fullname'],
		'html_url' => $repo['html_url']
	));	
	
}

echo "......................................................\n";
echo "Fetching weekly commits...............................\n";
echo "......................................................\n";

foreach($repos AS $repo) {

	$repo_authors = $client->api('repo')->statistics(ORGANISATION, $repo['name']);
	
	/*
	$organizationApi = $client->api('repositories');
	$paginator       = new \Github\ResultPager($client);
	$parameters      = array(ORGANISATION, $repo['name']);
	$repo_authors    = $paginator->fetchAll($organizationApi, 'statistics', $parameters);
	*/
	
	// Test query - SELECT datetime(week,'unixepoch'), * FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id WHERE login='cmorillo' ORDER BY week ASC;

	echo "******* ".$repo['name'] ."[".count($repo_authors)." authors] *******\n";

	foreach ($repo_authors AS $author_data) {
		// print_r($author_data);

		echo $author_data['author']['login'] . " [" . $author_data['total'] . "]\n";

		// Insert author
		
		db_insert ($db, 'authors', array(
			'id' => $author_data['author']['id'],
			'login' => $author_data['author']['login'],
			'gravatar_url' => $author_data['author']['gravatar_url'],
			'html_url' => $author_data['author']['html_url']
		));
	
		// if ($author_data['author']['id'] == '871106') { print_r($author_data); exit(); }
	
		
		foreach ($author_data['weeks'] AS $weekly_commits)  {
			
			// Insert weekly commit information
			
			// if ($author_data['author']['id'] == '871106') echo $weekly_commits['w'] . " - a:" . $weekly_commits['a']. " - d:" . $weekly_commits['d']. " - c:" . $weekly_commits['c']."\n";
			
			if ($weekly_commits['c'] > 0) {
				
				db_insert ($db, 'weekly_commits', array(
					'repo_id' => $repo['id'],
					'author_id' => $author_data['author']['id'],
					'week' => $weekly_commits['w'],
					'additions' => $weekly_commits['a'],
					'deletions' => $weekly_commits['d'],
					'commits' => $weekly_commits['c']
				));
			
			}
		}
	}
} 


?>
