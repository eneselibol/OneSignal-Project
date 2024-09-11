<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=onesignal_project;charset=utf8", 'username', 'password');
} catch (PDOExpception $e) {
    echo $e->getMessage();
}