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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/business-casual.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 50%;
            width: 80%;
            margin-left:9.95%;
        }
    </style>

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
    </div>
     <div id="map"></div>
        <!--The map will be put 'inside' this div -->
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: 1.3150701, lng: 103.7069311},
                    zoom: 10
                });
                /*
                 * initialise a map and display it on the page.
                 * The map is a content of the <div> with id 'map'(line 25)
                 */
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        /* Try HTML5 geolocation.
                         * Actual fetching of current coordinates of device. 
                         * 
                         */
                        var currMarker = new google.maps.Marker({
                            position: pos,
                            map: map
                        });
                        currMarker.setMap(map);
                        // create a red marker and put it on current location of device. 
                        map.setCenter(pos);
                        map.setZoom(13);
                        var destination = document.getElementById('address').value;
                        //destination is NP postal code hardcoded. 
                        var patternForSixDigits = /\d{6}/;
                        var checkPostalCode = patternForSixDigits.test(destination);
                        // regular expression for 6 digits, assume postal codes have 6 digits. 
                        var patternForAnyString = /.+/;
                        var checkForDestinationNotEmpty = patternForAnyString.test(destination);
                        //regular expression for any amount of character in strings, make sure destination is not empty
                        if (!checkForDestinationNotEmpty) {
                            //if destination is empty string, it means there are no packages. 
                            alert("There is currently no packages to deliver");
                        } else if (!checkPostalCode) {
                            // if postal code is not six digits. 
                            alert("Postal code for destination is invalid");
                        }
                        else if (checkForDestinationNotEmpty && checkPostalCode) {
                            var geocoder = new google.maps.Geocoder();
                            geocoder.geocode({'address': destination}, function (results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var lat = results[0].geometry.location.lat();
                                    var lng = results[0].geometry.location.lng();
                                    // takes the destination variable from line and gecode it
                                    var destInLatLng = new google.maps.LatLng(lat, lng);
                                    // that is the postal code is transformed into a latlng object.   
                                    var marker2 = new google.maps.Marker({
                                        position: destInLatLng,
                                        map: map,
                                        icon: 'img/blueMarker.png'
                                    });

                                    marker2.setMap(map);
                                    //  converting postal code to latlng is important
                                    //  because to put a marker on the map, it requires a latlng. 
                                }
                            });
                            var directionsService = new google.maps.DirectionsService();
                            var directionsRequest = {
                                origin: pos,
                                destination: destination,
                                /*
                                 * Send a request to google API to find out how to go from origin to destination
                                 */
                                travelMode: google.maps.DirectionsTravelMode.DRIVING,
                                unitSystem: google.maps.UnitSystem.METRIC
                            };
                            directionsService.route(directionsRequest, function (response, status) {
                                if (status == google.maps.DirectionsStatus.OK) {
                                    //response from google api on how to go from origin to destination
                                    var totalDurationNeed = 0;
                                    var timesToLoop = response.routes[0].legs[0].steps.length;
                                    /*
                                     * The response is a big javascript object. 
                                     * each journey from start to end is one 'leg'
                                     * inside one 'leg' there are many 'steps'. 
                                     * each step is one small part of the entire journey
                                     * duration needed is calculated and generated for each step. 
                                     */
                                    for (i = 0; i < timesToLoop; i++) {
                                        // for every step, sum up the time needed. meaning get amount of time for entire jounrey(leg). 
                                        var tempStringForDuration = response.routes[0].legs[0].steps[i].duration.text;
                                        // duration needed for each step is stored into a variable. at this point of time, duration is a string.
                                        // the variable now contains a value like '10mins'
                                        var pattern1 = /\d/g;
                                        var numOfMinsInArr = tempStringForDuration.match(pattern1);
                                        // use regex to extract out the integers from the string. 
                                        var addAllDigitsTogether = "";
                                        var numOfMinsToInt;
                                        for (j = 0; j < numOfMinsInArr.length; j++) {
                                            addAllDigitsTogether = addAllDigitsTogether + numOfMinsInArr[j];
                                        }
                                        /*from the example above, the 10mins become an array [1,0], thus need to add digits together
                                         * so that they become "10".
                                         * at this point of time, "10" is still a string
                                         */
                                        numOfMinsToInt = parseInt(addAllDigitsTogether);
                                        //convert "10" to 10 in integer form. 
                                        totalDurationNeed = totalDurationNeed + numOfMinsToInt;
                                        // add up all the time needed for each step. 
                                    }
                                    document.getElementById("displayTimeNeeded").innerHTML += totalDurationNeed;
                                    document.getElementById("displayTimeNeeded").innerHTML += "mins";
                                    var flightPath = new google.maps.Polyline({
                                        path: google.maps.geometry.encoding.decodePath(response.routes[0].overview_polyline),
                                        //geodesic: true,
                                        strokeColor: '#FF0000',
                                        strokeOpacity: 1.0,
                                        strokeWeight: 2
                                    });
                                    flightPath.setMap(map);
                                    //draw the lines on the map. Line shows how to go from origin to destination. 
                                }
                            });
                        }
                    }); //geolocation pos function ends here
                }
            }//init map ends here
        </script>   
    <div class="container">
        <div class="row">
            <h2 id="displayTimeNeeded" class="text-center">Estimated time: </h2>
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
                                <tbody id="packageTable">
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
                                                echo '<tr id="tuple'. $row['orderNo'] .'">';
                                                echo '<th>'. $row['sequence'] .'</th>';
                                                echo '<th>'. $row['orderNo'] .'</th>';
                                                echo '<th>'. $row['address'] .'</th>';
                                                echo '<th>'. $row['unitNumber'] .'</th>';
                                                echo '<th>'. $row['postalCode'] .'</th>';
                                                echo '<th>'. $row['custName'] .'</th>';
                                                echo '<th>'. $row['custContactNo'] .'</th>';
                                                if($row['status'] == 'Delivering'){
                                                    echo '<th>'. $row['status'] .'</th>';
                                                    echo '<input type="hidden" id="address" name="address" value="'. $row['postalCode'] .'">';
                                                }
                                                else{
                                                    echo '<th>';

                                                    //prioritize package Modal
                                                    echo '<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#prioritizePackageModal'.$row['orderNo'].'" id="'.$row['orderNo'].'" onclick="prioritizePackage('.$row['orderNo'].'); return false">Prioritize package</button>';
                                                    echo '<div class="modal fade" id="prioritizePackageModal'.$row['orderNo'].'" role="dialog">';
                                                    echo '<div class="modal-dialog modal-lg">';
                                                    echo '<div class="modal-content">';
                                                    echo '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h1 class="modal-title text-center">Prioritizing Package</h1></div>';
                                                    echo '<div class="modal-body">';      
                                                    echo '<ul class="nav navbar-nav">';          
                                                    echo '<li>';          
                                                    echo '<p>Current overall route</p>';        
                                                    echo '<div style="height:100%;width:100%;max-width:100%;list-style:none; transition: none;overflow:hidden;">';        
                                                    echo '<img src="img/currentMap.JPG" style="height:300px;width:350px" />';
                                                    echo '</div>';
                                                    echo '<p>Estimated time (mins): </p><p id="currentTime'.$row['orderNo'].'"></p>';
                                                    echo '</li>';
                                                    echo '<li>';
                                                    echo '<p>New overall route</p>';
                                                    echo '<div style="height:100%;width:100%;max-width:100%;list-style:none; transition: none;overflow:hidden;">';
                                                    echo '<img src="img/newMap.JPG" style="height:300px;width:350px" />';
                                                    echo '</div>';
                                                    echo '<p>Estimated time (mins): </p><p id="newTime'.$row['orderNo'].'"></p>';
                                                    echo '</li></ul></div>';
                                                    echo '<div class="modal-footer">';
                                                    echo '<div class="text-center">';
                                                    echo '<button type="button" class="btn btn-success" id="prioritizeButtonClicked'.$row['orderNo'].'" onclick="">Prioritize</button>';
                                                    echo '<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>';
                                                    echo '</div></div></div> </div> </div>';
                                                    echo '</th>';
                                                }
                                                echo '</tr>';

                                                if($row['status'] == 'Delivering'){
                                                    echo '<tr id="prioritizedTuplePosition"></tr>';
                                                }
                                            }
                                        }
                                    ?>
                                </tbody>
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
    <script src="js/bootstrap.min.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDBlPRqussTfegXfkko_dknboV8cips-hE&callback=initMap&libraries=geometry"></script>
</body>

</html>
