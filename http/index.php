<?php
	
require_once '../src/includes.php';	

// Connect to database, or create it if it doesn't exist
$db = new PDO('sqlite:'.SQLLITE_DB_FILENAME);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Github Organisation Contributor Statistics</title>

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
    
  </head>
  <body>
    <h1>Github Organisation Contributor Statistics</h1>
    
    
	<div class="container">
	    <div class="row">
	        <div class='col-sm-6'>
	            <div class="form-group">
	                <div class='input-group date' id='datetimepicker1'>
	                    <input type='text' class="form-control" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
	            </div>
	        </div>
	        <script type="text/javascript">
	            $(function () {
	                $('#datetimepicker1').datetimepicker({
		                format: 'DD/MM/YYYY'
	                });
	                	
	            });
	        </script>
	    </div>
	</div>

<div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker3'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker3').datetimepicker({
                    format: 'LT'
                });
            });
        </script>
    </div>
</div>

   
    
    
  </body>
</html>
