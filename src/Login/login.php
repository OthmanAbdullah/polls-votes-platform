<?php
include_once("../storage/userstorage.php");
include_once("../storage/storage.php");
include_once("../Auth/auth.php");

// functions
function redirect($page)
{
    header("Location: $page");
    exit();
}
function validate($post, &$data, &$errors)
{
    foreach ($errors as $i => $value) {
        unset($errors[$i]);
    }
    if (!isset($post['username']) || empty($post['username']) || preg_match('/[\s-]/', $post['username'])) {
        $errors["username"] = "Username cannot be empty or include spaces!";
    }

    if (!isset($post['password']) || empty($post['password'])) {
        $errors["password"] = "Password can't be empty!";
    }



    $data = $post;
    return count($errors) === 0;

    return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $auth_user = $auth->authenticate($data['username'], $data['password']);
        if (!$auth_user) {
            $errors['global'] = "User name or Password is incorrect !";
        } else {
            $auth->login($auth_user);
            redirect('../index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<style>
    body {
        font-family: "Roboto", sans-serif
    }

    .w3-bar-block .w3-bar-item {
        padding: 16px;
        font-weight: bold;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Style the navigation bar */
        .navbar {
            width: 100%;
            background-color: #555;
            overflow: auto;
        }

        /* Navbar links */
        .navbar a {
            float: left;
            text-align: center;
            padding: 12px;
            color: white;
            text-decoration: none;
            font-size: 17px;
        }

        /* Navbar links on mouse-over */
        .navbar a:hover {
            background-color: #000;
        }

        /* Current/active navbar link */
        .active {
            background-color: #04AA6D;
        }

        /* Add responsiveness - will automatically display the navbar vertically instead of horizontally on screens less than 500 pixels */
        @media screen and (max-width: 500px) {
            .navbar a {
                float: none;
                display: block;
            }
        }
    </style>
</head>
</head>

<body>
    <div class="collapse navbar-collapse ml-auto navbar">
        <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="../ActiveForms/activeForms.php"><i class="fa fa-fw fab fa-wpforms"></i> Active forms</a>
        <a href="../InActiveForms/inActiveForms.php"><i class="fa fa-fw fab fa-wpforms"></i> Inactive forms</a>
        <a class="active"  href="#"><i class="fa fa-fw fa-user"></i> Login</a>
        <a href="../Registration/register.php"> <i class="fa fa-fw fa-user"></i> Register</a>
    </div>

    <div class="login">
        <div class="center">
            <h1>Login</h1>
            <?php if (isset($errors['global'])) : ?>
                <section class="error"><?= $errors['global'] ?></section>
            <?php endif; ?>
            <br><br><br>
            <form action="" method="post">
                <div class="inputbox">
                    <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">

                    <?php if (isset($errors['username'])) : ?>
                        <span class="error"><?= $errors['username'] ?></span>
                    <?php endif; ?>
                    <span>Username</span>
                </div>
                <div class="inputbox">
                    <input type="password" name="password" id="password">
                    <?php if (isset($errors['password'])) : ?>
                        <span class="error"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                    <span>Password</span>
                </div>
                <div class="inputbox">
                    <button type="submit">Login</button>
                </div>
            </form>
            <section class="goLinks">
                <a href="../Registration/register.php">Register</a>
                <a href="../index.php"> Main Page</a>
            </section>
        </div>
    </div>
</body>

</html>