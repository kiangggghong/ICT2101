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

</head>

<body>
    <!--<div class="brand">Driver View</div>-->
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
            <h1 class="text-center">Driver Tracking System</h1>
            <p></p>

            <!--search bar-->
            <div class="input-group text-center" role="search">
                <input type="text" class="form-control" placeholder="Search for Package/Driver">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#searchModal">Go!</button>
                </span>
            </div>

            <!-- Modal for search bar-->
            <div class="modal fade" id="searchModal" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Search feature unavailable</h4>
                        </div>
                        <div class="modal-body col-lg-6">
                            <p>Work in progress. Thank you for your understanding.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--table of driver information-->
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr class="info">
                        <th>#</th>
                        <th>Driver Name</th>
                        <th>Vehicle Number</th>
                        <th>Driver Status</th>
                        <th>Next Destination</th>
                        <th>No. of Packages Left</th>
                        <th>More Details</th>
                    </tr>
                    <?php
                        $conn = dbConnect();

                        $select_query = "SELECT e.emp_id, e.firstName, d.vehiclePlateNumber, d.status, IFNULL(t2.TOTAL, 0) AS 'Total' FROM Employees e, Drivers d LEFT JOIN (SELECT de.vehiclePlateNumber, COUNT(de.orderNo) AS 'TOTAL' FROM Delivery de GROUP BY de.vehiclePlateNumber) AS t2 ON d.vehiclePlateNumber = t2.vehiclePlateNumber WHERE e.emp_id = d.emp_id";

                        $result = mysqli_query($conn, $select_query);
                        
                        if(mysqli_num_rows($result) > 0){
                            $count = 1;

                            while($row = mysqli_fetch_assoc($result)){
                                echo '<tr>';
                                echo '<th>'. $count .'</th>';
                                echo '<th>'. $row['firstName'] .'</th>';
                                echo '<th>'. $row['vehiclePlateNumber'] .'</th>';
                                echo '<th>'. $row['status'] .'</th>';
                                if($row['Total'] > 0){
                                    $select_location = "SELECT p.address FROM Packages p, Delivery d WHERE d.orderNo = p.orderNo AND p.status = 'Delivering' ORDER BY d.sequence";

                                    $result2 = mysqli_query($conn, $select_location);
                                    $row2 = mysqli_fetch_assoc($result2);

                                    if (mysqli_num_rows($result2) > 0){
                                        echo '<th>'. $row2['address'] .'</th>';
                                    }

                                }else{
                                    echo '<th>NIL</th>';
                                }
                                
                                echo '<th>'. $row['Total'] .'</th>';

                                if($row['Total'] > 0){
                                    echo '<th>';
                                    echo '<a href="driver_details.php?emp_id='. $row['emp_id'] .'" role="button" class="btn btn-success">CLICK HERE FOR MORE DETAIL</a>';
                                    echo '</th>';
                                }else {
                                    echo '<th>-</th>';
                                }
                                echo '</tr>';

                                $count++;
                            }
                        }
                    ?>
                </table>
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

                    <!-- title-->
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

                    <!--customer contact number-->
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
