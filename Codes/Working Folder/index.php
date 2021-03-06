﻿<?php
    session_start();
    include("classified/connectdb.php");

    if(isset($_SESSION['roles'])){
        $roles = $_SESSION['roles'];
        if($roles == 'Driver'){
            echo "<script> window.location = 'driver_landing.php'; </script>";
        }
    }

    $error = false;

    if(isset($_POST['submit'])){
        $userName = $_POST['username'];
        $userPassword = $_POST['password'];

        $conn = dbConnect();

        //Validation
        if(empty($userName)){
            $error = true;
        }

        if(empty($userPassword)){
            $error = true;
        }

        if(!$error){
            $userName = strtolower($userName);
            
            $query = "SELECT e.emp_id, r.r_id FROM Employees e, Roles r WHERE e.userName='$userName' AND e.password='$userPassword' AND e.r_id = r.r_id";

            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);

            if (mysqli_num_rows($result) > 0){
                $roles = $row["r_id"];
            //     //Set Session
                $_SESSION['EMP_ID'] = $row["emp_id"];
                $_SESSION['roles'] = $roles;


                if($roles == 1){
                    //header('location: Driver_mainPage.html');
                    echo "<script> window.location = 'driver_landing.php'; </script>";
                }
                elseif($roles == 2){
                    //header('Location: manager_landing.php');
                    echo "<script> window.location = 'manager_landing.php'; </script>";
                }
            } else{
                echo '<script language="javascript"> alert("Invalid Username/Password! Please try agian!")';
                echo '</script>';
            }
        }
        else{
            echo '<script language="javascript"> alert("All fields are required!")';
            echo '</script>';
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
    <!--main body-->
    <div class="container">
        <!--title-->
        <div class="jumbotron">
            <h1 class="text-center">Driver Tracking System</h1>
            <h2 class="text-center">Login</h2>
        </div>

        <!--login form-->
        <div class="row">
            <div class="Absolute-Center is-Responsive">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <form id="loginForm" method="POST" action="index.php">

                        <!-- username -->
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input class="form-control" type="text" id="username" name="username" placeholder="Username" />
                        </div>

                        <!--password-->
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input class="form-control" type="password" name="password" id="password" placeholder="Password" />
                        </div>

                        <!--login button-->
                        <div class="form-group">
                            <button type="submit" id="submit" name="submit" class="btn btn-def btn-info btn-block">Login</button>
                        </div>
                    </form>
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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
