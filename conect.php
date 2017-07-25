<?php
 	$dbserver = "localhost";
    $dbuser = "root";
    $password = "";
    $dbname = "chilexpress";
    
    $con = new mysqli($dbserver, $dbuser, $password, $dbname);
    
    if($con->connect_errno) {
        die("No se pudo conectar a la base de datos");
    }

?>