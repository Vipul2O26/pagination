<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pagination";

    try {
        $connect = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connect->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        //echo "connect successfully";
    } catch( Ecxeption $e ) {
        echo "Error : " . $e->getMessage();
    }
    
    







?>