<?php
include './db.php';

if (isset($_POST['email'])) {

    $email = $_POST['email'];

    $stmt = $connect->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        echo "false";
    } else {

        echo "true";
    }
}
?>