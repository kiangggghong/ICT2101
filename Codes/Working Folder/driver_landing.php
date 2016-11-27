<?php
    session_start();
    include("classified/connectdb.php");
    include("function.php");

    if(isset($_SESSION['roles'])){
        $roles = $_SESSION['roles'];
        $emp_id = $_SESSION['EMP_ID'];
    }else{
        echo "<script> window.location = 'index.php'; </script>";
    }

    $conn = dbConnect();

    $name = NULL;
    $select_query = "SELECT firstName FROM Employees WHERE emp_id='$emp_id'";
    $result = mysqli_query($conn, $select_query);
    
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $name = $row['firstName'];
        }
    }

    $total = getPackageCount($conn, $emp_id);

    $package_query = "SELECT p.* FROM Packages p, Delivery d , Drivers dr
                WHERE dr.emp_id = '$emp_id' AND d.vehiclePlateNumber = dr.vehiclePlateNumber
                AND p.status = 'Delivering' LIMIT 1";
    $result2 = mysqli_query($conn, $package_query);

    $postalCode = NULL;

    if(mysqli_num_rows($result2) > 0){
        while($row = mysqli_fetch_assoc($result2)){
            $postalCode = $row['postalCode'];
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <!-- <div class="brand">Driver View</div>-->
    <!--navbar-->
    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li><a href="driver_landing.php"><span class="glyphicon glyphicon-home"></span>Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $name; ?></a></li>
            </ul>
        </div>
    </nav>

    <!--main body-->
    <div class="container">

        <!--Added button to direct driver to package detail page-->
        <div class="row">
            <a href="packageDetails.php" class="btn btn-primary btn-lg btn-block" role="button">Packages left: <span class="badge"><?php if($total == 0){ echo '0'; } else { echo $total; } ?></span></a>
        </div>
        
        <!--google map-->
        <div id="map"></div>
        <input type="hidden" id="address" name="address" value="<?php echo $postalCode; ?>">            
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
                            alert(destination);
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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDBlPRqussTfegXfkko_dknboV8cips-hE&callback=initMap&libraries=geometry"></script>
</body>
</html>
