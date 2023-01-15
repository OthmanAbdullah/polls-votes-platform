<?php
include('../Storage/storage.php');
include('../Auth/auth.php');
include('../Storage/userstorage.php');

function redirect($page) {
  header("Location: $page");
  exit();
}

// input
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);

$auth->logout();
redirect('../index.php');