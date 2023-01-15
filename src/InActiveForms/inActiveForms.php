<?php
include_once("../storage/userstorage.php");
include_once("../storage/storage.php");
include_once("../Auth/auth.php");

function redirect($page)
{
    header("Location: $page");
    exit();
}
function isActiveDeadline($deadline)
{
    $current_date = date('Y-m-d', time());
    $current_date = new DateTime($current_date);
    $tempDate = new DateTime($deadline);
    // $deadline = new DateTime('2022-06-01');
    $interval = date_diff($current_date, $tempDate);
    // var_dump($deadline);
    // $current_date = (new DateTime())->format('Y-m-d');
    // // $deadline = new DateTime('2022-06-01');
    // $interval = $current_date->diff($deadline);

    if ($interval->invert == 1) {
        return false;
    } else {
        return true;
    }
}

// input
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$polls_storage = new UserStorage("../Storage/DB/polls.json");
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);


//data 
$polls = $polls_storage->findAll();

// proccessing

usort($polls, function ($a, $b) {
    $dateA = DateTime::createFromFormat('Y-m-d', $a["createdAt"]);
    $dateB = DateTime::createFromFormat('Y-m-d', $b["createdAt"]);
    return $dateB <=> $dateA;
});
if (isset($_POST["show"]) &&  $_POST["show"] == "show" && isset($_POST["poll-id"]) ) {

    $_SESSION["post_data"] = $_POST;
    header("Location: ../Show/showPage.php");
    exit();
    // redirect(`../Voting/votingPage.php?poll-id=63c0724d8947e`);

} else {

    if (isset($_POST["delete"])) {
        $polls_storage->delete($_POST["poll-id"]);
        redirect("inactiveForms.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="inactiveForms.css">
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
    <title>In Active Forms</title>
</head>

<body>

    <div class=" collapse navbar-collapse ml-auto navbar">
        <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="../ActiveForms/activeForms.php"><i class="fa-solid fa-list"></i> Active Forms</a>
        <a class="active" href="#"> <i class="fa-solid fa-list-check"></i> Inactive forms</a>
        <?php
        if (!$auth->is_authenticated()) : ?>
            <a href="../Login/login.php "><i class="fa fa-fw fa-user"></i> Login</a>
            <a href="../Registration/register.php"> <i class="fa fa-fw fa-user"></i> Register</a>
        <?php endif ?>
        <?php if ($auth->is_authenticated() && $auth->authenticated_user()['role'] === "admin") : ?>
            <a href="../PollCreation/pollCreation.php"> <i class="fa-regular fa-square-plus"></i> Poll Creation</a>
        <?php endif ?>
        <?php
        if ($auth->is_authenticated()) : ?>
            <a href="../Logout/logout.php"> <i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php endif ?>
    </div>

    <div class="card">
        <h1 class="card-title">Inactive Forms</h1>
        <div class="card-body">
            <?php foreach ($polls as $poll) : ?>
                <?php if (!isActiveDeadline($poll["deadline"])) : ?>
                    <form action="" method="post">
                        <div class="poll">
                            <input name="poll-id" value="<?= $poll["id"] ?>" type="text" hidden>
                            <div class="poll-id">Poll ID: #<?= $poll["id"] ?></div>
                            <div class="poll-date">Created: <?= $poll["createdAt"] ?></div>
                            <div class="poll-deadline">Deadline: <?= $poll["deadline"] ?></div>
                            <button class="poll-button" type="submit" name="show" value="show" >Show</button>
                            <?php if ($auth->is_authenticated() &&  $auth->authenticated_user()['role'] == "admin") : ?>
                                <button class="poll-button" type="submit" name="delete" value="delete">Delete
                                <button class="poll-button" type="submit" name="edit" value="edit">Edit
                            <?php endif ?>
                        </div>
                    </form>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
</body>

</html>