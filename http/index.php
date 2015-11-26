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
    
   <style type="text/css">
		@media screen and (min-width: 768px) {
		    #adv-search {
		        width: 500px;
		        margin: 0 auto;
		    }

		}
		
		
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
			<h2>Search by user</h2>
		</div>
    
		<form method="get" action="">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
			            <div class="input-group" id="adv-search">
			                <input type="text" class="form-control" placeholder="Enter a github username" />
			                <div class="input-group-btn">
			                    <div class="btn-group" role="group">
			                        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
			                    </div>
			                </div>
			            </div>
					</div>
				</div>
			</div>
		</form>

		
		
     	<div class="page-header">
			<h2>Search by date</h2>
		</div>
    
		<form method="get" action="">
		    <div class="container">
			    <div class='col-md-4'>
			        <div class="form-group">
			            <div class='input-group date' id='datetimepicker6'>
			                <input type='text' class="form-control" name="date_from" placeholder="From:" />
			                <span class="input-group-addon">
			                    <span class="glyphicon glyphicon-calendar"></span>
			                </span>
			            </div>
			        </div>
			    </div>
			    <div class='col-md-4'>
			        <div class="form-group">
			            <div class='input-group date' id='datetimepicker7'>
			                <input type='text' class="form-control" name="date_from" placeholder="To:" />
			                <span class="input-group-addon">
			                    <span class="glyphicon glyphicon-calendar"></span>
			                </span>
			            </div>
			        </div>
			    </div>
			    <div class='col-md-4'>
			        <div class="form-group">
				        <div class='input-group' id='datebutton'>
							<button type="submit" class="btn btn-default">Submit</button>
				        </div>
			        </div>
			    </div>
			</div>
			<script type="text/javascript">
			    $(function () {
			        $('#datetimepicker6').datetimepicker();
			        $('#datetimepicker7').datetimepicker({
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

		<div class="page-header">
			<h2>Results</h2>
		</div>
		
		
		<div class="col-md-6">
          <table class="table table-condensed">
            <thead>
              <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
              </tr>
              <tr>
                <td>3</td>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
              </tr>
            </tbody>
          </table>
        </div>




	</div>
    
    
  </body>
</html>
