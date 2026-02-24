<?php
include './db.php';

$email = $_POST['email'];
$id    = $_POST['id'] ?? 0;

$stmt = $connect->prepare("SELECT id FROM users WHERE email=? AND id!=?");
$stmt->execute([$email, $id]);

if ($stmt->rowCount() > 0) {
    echo "false"; 
} else {
    echo "true";  
}