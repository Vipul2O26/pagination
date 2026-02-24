<?php

    ini_set( 'display_errors', 1 );
    ini_set( 'display_startup_errors', 1 );
    error_reporting( E_ALL );

    session_start();

    include './db.php';
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";

    echo $delete_id = $_GET['id'];

    try {
        
        $sql_delete = "DELETE FROM `users` WHERE `users`.`id` = $delete_id";
        
        $connect->exec($sql_delete);
        $_SESSION['msg'] = "user delete successfully";        
        header("Location: list.php");
    } catch ( Exception $e ){
        echo "Error : " .$e->getMessage();
    } finally{
        $sql_delete = null;
        $connect = null;

    }
    

?>