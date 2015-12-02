<?php
	
require_once '../src/includes.php';	

// Connect to database, or create it if it doesn't exist
$db = new PDO('sqlite:'.SQLLITE_DB_FILENAME);

// $_GET['output_type'] = 'percentage';

// Default date ranges

$time = time();

if (!isset($_GET['date_from'])) {
  if (date('m', $time) >= 10) {
    $_GET['date_from'] = '01-10-'.(date('Y', $time)-1);
  } else {
    $_GET['date_from'] = '01-10-'.(date('Y', $time)-2);
  }
}

if (!isset($_GET['date_to'])) {
  if (date('m', $time) >= 10) {
    $_GET['date_to'] = '30-09-'.(date('Y', $time));
  } else {
    $_GET['date_to'] = '30-09-'.(date('Y', $time)-1);
  }
}

$report_types = array (
  'commits_by_week' => 'By Week',
  'commits_by_project' => 'By Project',
  'commits_by_author' => 'By Author'
);

$output_types = array (
  'absolute' => 'Absolute Values',
  'percentage' => 'Percentage'
);

// Get Author Info

$sql = 'SELECT * FROM authors WHERE 1';
$stmt = $db->prepare($sql);
$query = $stmt->execute(array());
if (!$query) {
	die(print_r($db->errorInfo()) );
}

while ($row = $stmt->fetch()) {
	$authors[$row['id']] = $row;
}

// Get Repo Info

$sql = 'SELECT * FROM repos WHERE 1';
$stmt = $db->prepare($sql);
$query = $stmt->execute(array());
if (!$query) {
	die(print_r($db->errorInfo()) );
}

while ($row = $stmt->fetch()) {
	$repos[$row['id']] = $row;
}

if (isset($_GET['report_type'])) {
  
  // Get Weekly Commits
  
  $sql = 'SELECT author_id, repo_id, week, additions, deletions, commits FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id WHERE week >= ? AND week <= ? ORDER BY week ASC';
  // $debug_sql = 'SELECT author_id, repo_id, week, additions, deletions, commits FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id WHERE week >= '.strtotime($_GET['date_from']).' AND week <= '.strtotime($_GET['date_to']).' ORDER BY week ASC';
  // echo $debug_sql;
  $stmt = $db->prepare($sql);
  $query = $stmt->execute(array(strtotime($_GET['date_from']),strtotime($_GET['date_to'])));
  if (!$query) {
  	die(print_r($db->errorInfo()) );
  }
  
  // echo $stmt->debugDumpParams();
  
  while ($row = $stmt->fetch()) {
  	$commits_map [$row['author_id']] [$row['repo_id']] [$row['week']] ['additions'] = $row ['additions'];
  	$commits_map [$row['author_id']] [$row['repo_id']] [$row['week']] ['deletions'] = $row ['deletions'];
  	$commits_map [$row['author_id']] [$row['repo_id']] [$row['week']] ['commits'] = $row ['commits'];
  	
  	$weeks[$row['week']] = $row['week'];
  	
  	$commits_author_repo [$row['author_id']] [$row['repo_id']] ['total_commits'] += $row ['commits'];
  	$repos[$row['repo_id']]['total_commits'] += $row ['commits'];
  	$authors[$row['author_id']]['total_commits'] += $row ['commits'];
  }
  
  asort($weeks);
  
  foreach ($authors AS $author_id => $author) {
    if ($author['total_commits'] > 0) {
      $author_commits[$author_id] = $author['total_commits'];
    } else {
      // Remove authors without any commits
      unset($authors[$author_id]);
    }
  }
  
  foreach ($repos AS $repo_id => $repo) {
    if ($repo['total_commits'] > 0) {
      $repo_commits[$repo_id] = $repo['total_commits'];
    } else {
      // Remove repos without any commits
      unset($repos[$repo_id]);
    }
  }
  
  arsort($author_commits);
  arsort($repo_commits);
  
}


/*
echo '<pre>';
print_r($authors);
echo '</pre>';
*/


// SELECT datetime(week,'unixepoch'), * FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id WHERE 1 ORDER BY week ASC;
// SELECT *, SUM(wc.commits) FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id GROUP BY r.id, a.id ORDER BY a.id ASC;
// SELECT *, SUM(wc.commits) FROM weekly_commits AS wc INNER JOIN authors AS a ON wc.author_id = a.id INNER JOIN repos AS r ON wc.repo_id=r.id WHERE a.id=245434 GROUP BY r.id ORDER BY a.id ASC


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Contributor Statistics</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/components/bootstrap/css/bootstrap.min.css">
    
    <!-- DateTimePicker-->
    <link rel="stylesheet" href="/components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/components/jquery/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/components/bootstrap/js/bootstrap.min.js"></script>
    
    <!-- DataTimePicker http://eonasdan.github.io/bootstrap-datetimepicker/Installing/ -->
    <script type="text/javascript" src="/components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="/components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    
    <style type='text/css'>
        .container { margin-left: 10px; font-size:10pt}
    </style>
    
  </head>
  <body>
	  
	<div class="container">
	   
		<!-- Main jumbotron for a primary marketing message or call to action -->
		<div class="jumbotron">
			<h1>Contributor Statistics</h1>
			<p>A tool to display all Github contributor statistics for <? echo ( defined('ORGANISATION') ? ORGANISATION : ' an organisation');?>.</p>
		</div>	 
 	
     	<div class="page-header">
			<h2>Search by date</h2>
		</div>
    
		<form method="get" action="">
		    <div class="container">
			    <div class='col-md-3'>
			        <div class="form-group">
			            <div class='input-group date' id='datetimepicker6'>
			                <input type='text' class="form-control" name="date_from" placeholder="From:" value="<? echo ( isset($_GET['date_from']) ? $_GET['date_from'] : '');?>" />
			                <span class="input-group-addon">
			                    <span class="glyphicon glyphicon-calendar"></span>
			                </span>
			            </div>
			        </div>
			    </div>
			    <div class='col-md-3'>
			        <div class="form-group">
			            <div class='input-group date' id='datetimepicker7'>
			                <input type='text' class="form-control" name="date_to" placeholder="To:" value="<? echo ( isset($_GET['date_to']) ? $_GET['date_to'] : '');?>" />
			                <span class="input-group-addon">
			                    <span class="glyphicon glyphicon-calendar"></span>
			                </span>
			            </div>
			        </div>
			    </div>
			    <div class='col-md-3'>
			        <div class="form-group">
				        <div class='input-group' id='reporttypedropdown'>
                  <select class="form-control" name="report_type">
                    <? foreach ($report_types AS $report_key => $report_name) { ?> 
                      <option value="<?= $report_key; ?>"<? echo ( isset($_GET['report_type']) && $_GET['report_type'] == $report_key ? ' selected' : ''); ?>><?= $report_name; ?></option>
                    <? } ?>
                  </select>
				        </div>
			        </div>
              <div class="form-group">
				        <div class='input-group' id='reporttypedropdown'>
                  <select class="form-control" name="output_type">
                    <? foreach ($output_types AS $output_key => $output_name) { ?> 
                      <option value="<?= $output_key; ?>"<? echo ( isset($_GET['output_type']) && $_GET['output_type'] == $output_key ? ' selected' : ''); ?>><?= $output_name; ?></option>
                    <? } ?>
                  </select>
				        </div>
			        </div>
			    </div>
          <div class='col-md-3'>
			        <div class="form-group">
				        <div class='input-group' id='datebutton'>
                  <button type="submit" class="btn btn-default" name="button" value="commits_by_week">Submit</button>
				        </div>
			        </div>
			    </div>
			</div>
			<script type="text/javascript">
			    $(function () {
			        $('#datetimepicker6').datetimepicker({
				        format: "DD-MM-YYYY"
			        });
			        $('#datetimepicker7').datetimepicker({
			            format: "DD-MM-YYYY",
			            useCurrent: false //Important! See issue #1075
			        });
			        $("#datetimepicker6").on("dp.change", function (e) {
			            $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
			        });
			        $("#datetimepicker7").on("dp.change", function (e) {
			            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
			        });
			    });
			</script>
   
		
		</form>

<?  switch ($_GET['report_type']) {  
      
      case 'commits_by_week': ?>

  		<div class="page-header">
  			<h2>Commits by week</h2>
  		</div>
  		
  		<table class="table table-condensed">
       	<thead>
          <tr>
            <th>User</th>
            <th>Repository</th><?
              foreach ($weeks AS $week_id => $week) {
                $i++;
                echo "          <th>".$i."</th>\n";
              }
            ?>
          </tr>
        </thead>
        <tbody><?
  			foreach ($authors AS $author_id => $author) { 
  				foreach ($repos AS $repo_id => $repo) { 
  					if (isset($commits_map[$author_id][$repo_id])) { 				
  						echo "\n";
  						echo "        <tr>\n";
  						echo "          <td>".$author['login'] ."</td>\n";
  						echo "          <td>".$repo['name'] ."</td>\n";			
              foreach ($weeks AS $week_id => $week) {
                if (isset($commits_map[$author_id][$repo_id][$week_id])) {
                  
                  // echo "          <td>".$commits_map[$author_id][$repo_id][$week_id]['commits']."</td>\n";
                  echo "          <td>".display_commits($_GET['output_type'], $commits_map[$author_id][$repo_id][$week_id]['commits'], $authors[$author_id]['total_commits'])."</td>\n";
  							} else {
    							echo "          <td>&nbsp;</td>\n";
  							}
  						} 
  						echo "        </tr>";
  					} 
  				}
  			} ?>
  			</tbody>
  		</table>
  		
<?    break; ?>		
<?    case 'commits_by_author': ?>
		
				<div class="col-md-6">
          <table class="table table-condensed">
           	<thead>
             	<tr>
                <th>#</th>
                <th>User</th>
                <th>Commits</th>
              </tr>
            </thead>
            <tbody>
            <? $i=0; ?>
            <? foreach ($author_commits AS $author_id => $num_commits) { $i++; ?>
              <tr>
                <td><?= $i?></td>
                <td><?= $authors[$author_id]['login']?></td>
                <td><?= $num_commits?></td>
              </tr>
            <? } ?>
            </tbody>
          </table>
        </div>
		
<?    break; ?>

<?    case 'commits_by_project': ?>
		
				<div class="col-md-6">
          <table class="table table-condensed">
           	<thead>
             	<tr>
                <th>User</th>
                <th>Project</th>
             	</tr>
                
             	</tr>
             	  <th>&nbsp;</th><?
                foreach ($repo_commits AS $repo_id => $num_commits) {
                  $i++;
                  echo "          <th>".$repos[$repo_id]['name']."</th>\n";
                }
              ?>
                <th>TOTALS</th>
              </tr>
            </thead>
            <tbody>
            <? $i=0; ?>
            <? foreach ($author_commits AS $author_id => $num_commits) { ?>
              <tr>
              <td><?= $authors[$author_id]['login']?></td>
              <?  foreach ($repo_commits AS $repo_id => $num_commits) { ?>
                <td><?= display_commits($_GET['output_type'], $commits_author_repo[$author_id][$repo_id]['total_commits'], $authors[$author_id]['total_commits']); ?></td>
              <?  } ?>
                <td><?= $authors[$author_id]['total_commits'] ?></td>
              </tr>
            <? } ?>
            </tbody>
          </table>
        </div>
		
<?    break; ?>

<? } ?>

	</div>
    
    
  </body>
</html>
