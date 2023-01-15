<?php
include_once("../storage/userstorage.php");
include_once("../storage/storage.php");
include_once("../Auth/auth.php");

function redirect($page)
{
    header("Location: $page");
    exit();
}

// input
// session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$polls_storage = new UserStorage("../Storage/DB/polls.json");
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);

function validate($get, &$data, &$errors)
{
    $data = $get;
    return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);
$data = [];
$errors = [];
// print_r($_GET);
if(isset( $_SESSION["post_data"])){
    $_GET = $_SESSION["post_data"];
}
if (count($_GET) > 0) {
    if (validate($_GET, $data, $errors)) {
    }
}

//data 
$poll = $polls_storage->findById($data["poll-id"]);
$answers  =  $poll["answers"];
unset($_SESSION["post_data"]);
?>


<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="showPage.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css%22">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>show poll result</title>
</head>

<body>
    <div class=" collapse navbar-collapse ml-auto navbar">
        <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a  href="../ActiveForms/activeForms.php"><i class="fa-solid fa-list"></i> Active Forms</a>
        <a class="active" href="../InActiveForms/inActiveForms.php"> <i class="fa-solid fa-list-check"></i> Inactive Forms</a>
        <?php
        if (!$auth->is_authenticated()) : ?>
            <a href="../Login/login.php"><i class="fa fa-fw fa-user"></i> Login</a>
            <a href="../Registration/register.php"> <i class="fa fa-fw fa-user"></i> Register</a>
        <?php endif ?>
        <?php if ($auth->is_authenticated() && $auth->authenticated_user()['role'] === "admin") : ?>
            <a href="../PollCreation/pollCreation.php"> <i class="fa-regular fa-square-plus"></i> Poll Creation</a>
        <?php endif ?>
        <?php
        if ($auth->is_authenticated()) : ?>
            <a href="Logout/logout.php"> <i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php endif ?>
    </div>
    <div class="card">
        <h1 class="card-title">
            Active Poll
            <div class="poll-date">Created: <span><?= $poll["createdAt"] ?></span></div>
            <div class="poll-deadline">Deadline: <span><?= $poll["deadline"] ?></span></div>
        </h1>
        <div class="poll-description">
            <p><?= $poll["question"] ?></p>
        </div>
        <div class="poll-options">
            <?php foreach ($poll["answers"] as $option) : ?>
                <div class="poll-option option-card">
                    <label for="option1"><?=key((array)$option)?></label>
                    <span class="vote-count"> <?=current($option)?> votes</span>
                </div>
            <?php endforeach ?>
        </div>
        <div class="poll-submit-wrap">
            <!-- <button class="poll-submit" type="submit">Back</button> -->
            <a href="../InActiveForms/inActiveForms.php"> <button  class="poll-submit" type="submit">Back</button></a>

        </div>
    </div>

</body>

</html>