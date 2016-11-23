<?php
    session_start();
    include("classified/connectdb.php");

    if(isset($_SESSION['roles'])){
        $roles = $_SESSION['roles'];
        $emp_id = $_SESSION['EMP_ID'];
        if($roles == 'Driver'){
            echo "<script> window.location = 'driver_landing.php'; </script>";
        }
    }else{
        echo "<script> window.location = 'index.php'; </script>";
    }

    if(isset($_GET['emp_id'])){
    	$emp_id = $_GET['emp_id'];
    }

   $conn = dbConnect();
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Driver Management Application</title>

    <!-- (Need to have this to make the page as what it is) -->
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">

    <!-- Fonts
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">
    -->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- <div class="brand">Driver View</div>-->
    <!--navbar-->
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="Manager_mainPage.html"><span class="glyphicon glyphicon-home"></span>Home</a></li>
                <li><a href="#" data-toggle="modal" data-target="#addPackageModal">Add Package</a></li>
                <li><a href="#" data-toggle="modal" data-target="#assignPackageModal">Assign Package</a></li>
                <li class="navbar-right"><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
            </ul>
        </div>
    </nav>

    <!--main body-->
    <div class="container">

        <!--title of page-->
        <div class="jumbotron">
        	<?php
        		$select_emp = "SELECT firstName FROM Employees WHERE emp_id = '$emp_id'";

        		$result = mysqli_query($conn, $select_emp);
	            $row = mysqli_fetch_assoc($result);
				
	            if (mysqli_num_rows($result) > 0){
	            	echo '<h1 class="text-center">Tracking Driver: '. $row['firstName'] .'</h1>';
	            }
        	?>
        </div>

        <!--google map-->
        <div class="row">
            <div style="height:440px;width:500%;max-width:100%;list-style:none; transition: none;overflow:hidden;">
                <div id="gmap_display" style="height:440px; width:100%;max-width:100%;"><iframe style="height:100%;width:100%;border:0;" frameborder="0" src="https://www.google.com/maps/embed/v1/directions?origin=Raffles+Place+Singapore&destination=Singapore+Institute+of+Technology+Singapore&key=AIzaSyAN0om9mFmy1QN6Wf54tXAowK4eT0ZUPrU"></iframe></div>
            </div>
        </div>

        <!--estimated time-->
        <div class="row">
            <h2 class="text-center">Estimated time: 14 mins++</h2>
        </div>

        <!--collapsible table-->
        <!--information on packages that driver is delivering-->
        <div class="row">
            <div class="panel-group">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#collapse1">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                More details on driver's packages
                            </h4>
                        </div>
                    </a>

                    <!--headers-->
                    <div id="collapse1" class="panel-collapse panel-body collapse">
                        <div class="table-responsive col-md-12">
                            <table class="table table-striped table-bordered">
                                <tr class="info">
                                    <th>Delivery Sequence</th>
                                    <th>Package ID</th>
                                    <th>Package Location</th>
                                    <th>Package Unit Number</th>
                                    <th>Package Postal Code</th>
                                    <th>Customer Name</th>
                                    <th>Customer Contact Number</th>
                                    <th>Status</th>
                                </tr>
                                <?php
                                	$select_packages = "SELECT d.sequence, p.orderNo, p.address, p.unitNumber, p.postalCode, p.custName, p.custContactNo, p.status 
                                	FROM Delivery d, Packages P WHERE d.vehiclePlateNumber = (SELECT dr.vehiclePlateNumber 
                                		FROM Drivers dr WHERE dr.emp_id = '$emp_id') AND d.orderNo = p.orderNo ORDER BY d.sequence";

                                	$result = mysqli_query($conn, $select_packages);
                        
                        			if(mysqli_num_rows($result) > 0){
                        				while($row = mysqli_fetch_assoc($result)){
	                        				echo '<tr>';
	                        				echo '<th>'. $row['sequence'] .'</th>';
	                        				echo '<th>'. $row['orderNo'] .'</th>';
	                        				echo '<th>'. $row['address'] .'</th>';
	                        				echo '<th>'. $row['unitNumber'] .'</th>';
	                        				echo '<th>'. $row['postalCode'] .'</th>';
	                        				echo '<th>'. $row['custName'] .'</th>';
	                        				echo '<th>'. $row['custContactNo'] .'</th>';
	                        				if($row['status'] == 'Delivering'){
	                        					echo '<th>'. $row['status'] .'</th>';
	                        				}
	                        				else{
	                        					echo '<th id="xx">';

                                        		//prioritize package Modal
                                        		echo '<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#prioritizePackageModal" id="hidePrioritizeButton" onclick="hidePrioritizeButton()">Prioritize package</button>';
                                        		echo '</th>';
	                        				}
	                        				echo '</tr>';
	                        			}
                        			}
                                ?>
                                <!--prioritize package Modal -->
                                <div class="modal fade" id="prioritizePackageModal" role="dialog">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h1 class="modal-title text-center">Prioritizing Package</h1>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="nav navbar-nav">
                                                    

                                                    <!--current route-->
                                                    <li>
                                                        <p>Current route</p>
                                                        <div style="height:100%;width:100%;max-width:100%;list-style:none; transition: none;overflow:hidden;">
                                                            <div id="gmap_display" style="height:100%; width:100%;max-width:100%;"><iframe style="height:300px;width:430px;border:2;" frameborder="1" src="https://www.google.com/maps/embed/v1/directions?origin=Raffles+Place+Singapore&destination=Singapore+Institute+of+Technology+Singapore&key=AIzaSyAN0om9mFmy1QN6Wf54tXAowK4eT0ZUPrU"></iframe></div>
                                                        </div>
                                                        <p>Estimated time: 14 mins++</p>
                                                    </li>
                                                    <!--new route-->
                                                    <li>
                                                        <p>New route</p>
                                                        <div style="height:100%;width:100%;max-width:100%;list-style:none; transition: none;overflow:hidden;">
                                                            <div id="gmap_display" style="height:100%; width:100%;max-width:100%;"><iframe style="height:300px;width:430px;border:2;" frameborder="1" src="https://www.google.com/maps/embed/v1/directions?origin=Raffles+Place+Singapore&destination=Tampines+Street+11+Singapore&key=AIzaSyAN0om9mFmy1QN6Wf54tXAowK4eT0ZUPrU"></iframe></div>
                                                        </div>
                                                        <p>Estimated time: 20 mins++</p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-success" id="prioritizeButtonClicked" onclick="">Prioritize</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--footer-->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <p>Copyright &copy; Driver Management 2016</p>
                </div>
            </div>
        </div>
    </footer>

    <!--assign package popover-->
    <div id="assignPackageModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="form-group">

                    <!--popover title-->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h1 class="modal-title text-center">Assign Package</h1>
                    </div>
                    <p></p>

                    <!--assign Package-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Package ID<t><t></label>
                    <div class="btn-group">
                        <button type="Packagesbutton" class="btn btn-default selectedOption">Select Package to be assigned</button>
                        <button type="Packagesbutton" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">124</a></li>
                        </ul>
                    </div>
                    <br><br>

                    <!--assign driver-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Driver Name<t><t></label>
                    <div class="btn-group">
                        <button type="DriverButton" class="btn btn-default selectedOption">Select Driver to be assigned</button>
                        <button type="DriverButton" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Bob</a></li>
                        </ul>
                    </div>
                    <br><br>
                    <div class="text-center">
                        <button type="Confirm" class="btn btn-primary text-center" data-dismiss="modal">Assign</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!--add package popover-->
    <div id="addPackageModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="form-group">

                    <!--popover title-->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h1 class="modal-title text-center">Add Package</h1>
                    </div>
                    <p></p>

                    <!--package id-->
                    <fieldset disabled>
                        <label class="col-sm-2 col-sm-offset-2 control-label">Package ID</label>
                        <div class="col-sm-6">
                            <input type="Pid" id="Pid" placeholder="124" class="form-control">
                        </div><br><br><br>
                    </fieldset>

                    <!--package location-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Package Location</label>
                    <div class="col-sm-6">
                        <input type="Location" id="Location" placeholder="Package Location" class="form-control">
                    </div><br><br><br>

                    <!--package unit number-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Package Unit Number</label>
                    <div class="col-sm-6">
                        <input type="Unit Number" id="Unit Number" placeholder="Package Unit Number" class="form-control">
                    </div><br><br><br>

                    <!--package postal code-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Package Postal Code</label>
                    <div class="col-sm-6">
                        <input type="Postal Code" id="Postal Code" placeholder="Package Postal Code" class="form-control">
                    </div><br><br><br>

                    <!--customer name-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Customer Name</label>
                    <div class="col-sm-6">
                        <input type="CName" id="CName" placeholder="Customer Name" class="form-control">
                    </div><br><br><br>

                    <!--customer phone-->
                    <label class="col-sm-2 col-sm-offset-2 control-label">Customer Contact Number</label>
                    <div class="col-sm-6">
                        <input type="Phone" id="Phone" placeholder="Customer Contact Number" class="form-control">
                    </div><br><br><br>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary text-center" data-dismiss="modal">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
