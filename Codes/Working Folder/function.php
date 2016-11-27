<?php
	function getPackageCount($conn, $emp_id){
		$total = 0;
		$select_query = "SELECT IFNULL(t2.TOTAL, 0) AS 'Total' FROM Employees e, Drivers d LEFT JOIN (SELECT de.vehiclePlateNumber, COUNT(de.orderNo) AS 'TOTAL' FROM Delivery de GROUP BY de.vehiclePlateNumber) AS t2 ON d.vehiclePlateNumber = t2.vehiclePlateNumber WHERE e.emp_id = '$emp_id'";

		$result = mysqli_query($conn, $select_query);
        if(mysqli_num_rows($result) > 0){
        	while($row = mysqli_fetch_assoc($result)){
        		$total = $row['Total']; 
        	}
        }

        return $total;
	}
?>