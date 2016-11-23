 <?php
 	//Author: Law Kiang Hong
 	//This script contain function to connect/disconnect from database
 
	function dbConnect(){
		$servername = 'localhost';
		$username = 'root';
		$password = 'root';
		$database = 'ict2101';

		$conn = mysqli_connect($servername, $username, $password,$database);

		if (!$conn) {
		    echo "Error: Unable to connect to MySQL." . PHP_EOL;
		    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		    exit;
		}

		return $conn;
		
	}

	function dbDisconnect($myConn)
	{
		mysqli_close($myConn);
	}

?>