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
                <li><a href="driver_landing.php"><span class="glyphicon glyphicon-home"></span>Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $name; ?></a></li>
            </ul>
        </div>
    </nav>

    <!--main body-->
    <div class="container">

        <!--Title of page-->
        <div class="jumbotron">
            <h1 class="text-center">Package details</h1>
            <h3 class="text-center"><?php if($total == 0){ echo '0'; } else { echo $total; } ?> packages left</h3>
        </div>

        <!-- Panel for current delivery -->
        <h4 class="text-center">Current delivery</h4>
        <div class="panel panel-danger">
            <?php 
                $getDeliveryPackage = "SELECT p.* FROM Packages p, Delivery d , Drivers dr
                WHERE dr.emp_id = '$emp_id' AND d.vehiclePlateNumber = dr.vehiclePlateNumber
                AND p.status = 'Delivering' LIMIT 1";

                $result = mysqli_query($conn, $getDeliveryPackage);
                $row = mysqli_fetch_assoc($result);

                echo '<div class="panel-heading"><h4>Package ID: '. $row['orderNo'] .'</h4></div>';
                echo '<div class="panel-body">';
                echo '<p>Location: '. $row['address'] .'</p>';
                echo '<p>Unit Number: #'. $row['unitNumber'] .'</p>';
                echo '<p>Postal code: '. $row['postalCode'] .'</p>';
                echo '<p>Customer: '. $row['custName'] .'</p>';
                echo '<p>Customer HP: '. $row['custContactNo'].'</p>';

                if($row['status'] == 'Prioritized'){
                    echo  '<p>Prioritiy Status: '. $row['status'] .'</p>';
                }else{
                    echo  '<p>Prioritiy Status: Nil</p>';
                }

                echo '</div>';
            ?>
        </div>

        <!--Panels for pending deliveries-->
        <h4 class="text-center">Pending deliveries  </h4>
        <div class="panel panel-info">
            <?php 
                $getDeliveryPackage = "SELECT DISTINCT p.* FROM Packages p, Delivery d , Drivers dr
                WHERE dr.emp_id = '$emp_id' AND d.vehiclePlateNumber = dr.vehiclePlateNumber
                AND p.status <> 'Delivering' ORDER BY p.status";

                $result = mysqli_query($conn, $getDeliveryPackage);
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo '<div class="panel-heading"><h4>Package ID: '. $row['orderNo'] .'</h4></div>';
                        echo '<div class="panel-body">';
                        echo '<p>Location: '. $row['address'] .'</p>';
                        echo '<p>Unit Number: #'. $row['unitNumber'] .'</p>';
                        echo '<p>Postal code: '. $row['postalCode'] .'</p>';
                        echo '<p>Customer: '. $row['custName'] .'</p>';
                        echo '<p>Customer HP: '. $row['custContactNo'].'</p>';

                        if($row['status'] == 'Prioritized'){
                            echo  '<p>Prioritiy Status: '. $row['status'] .'</p>';
                        }else{
                            echo  '<p>Prioritiy Status: Nil</p>';
                        }

                        echo '</div>';
                    }
                }
            ?>
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

</body>

</html>
