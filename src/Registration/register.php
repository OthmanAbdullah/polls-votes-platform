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




//print_r($user_Strg->findMany(function ($user){return $user['email'] === $_POST['email'] ;}));

function validate($post, &$data, &$errors)
{
    foreach ($errors as $i => $value) {
        unset($errors[$i]);
    }
    if (!isset($post['username']) || empty($post['username']) || preg_match('/[\s-]/', $post['username'])) {
        $errors["username"] = "Username cannot be empty or spaces!";
    }

    if (!isset($post['email']) || empty($post['email']) || preg_match('/[\s-]/', $post['email'])) {
        $errors["email"] = "Email cannot be empty or with spaces!";
    }
    if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format!";
    }

    if (!isset($post['password']) || empty($post['password'])) {
        $errors["password"] = "Password cannot be empty!";
    }
    if (!isset($post['repassword']) || empty($post['repassword'])) {
        $errors["repassword"] = "Password cannot be empty!";
    }
    if ($post['password'] != $post['repassword']) {
        $errors["password"] = "Password does not match!";
    }
    $data = $post;
    return count($errors) === 0;
}
// main
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        if ($auth->user_exists($data['username'])) {
            $errors['global'] = "User already exists";
        } else
    if (count($user_storage->findMany(function ($user) use ($data) {
            return $user['email'] === $data['email'];
        })) > 0) {
            $errors["global"] = "Email already used!";
        } else {
            $auth->register($data);
            redirect('../Login/login.php');
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
    <link rel="stylesheet" href="register.css">
    <title>Registeration</title>
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

<body>
    <div class="collapse navbar-collapse ml-auto navbar">
        <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="../ActiveForms/activeForms.php"><i class="fa fa-fw fab fa-wpforms"></i> Active forms</a>
        <a href="../InActiveForms/inActiveForms.php"><i class="fa fa-fw fab fa-wpforms"></i> Inactive forms</a>
        <a href="../Login/login.php "><i class="fa fa-fw fa-user"></i> Login</a>
        <a class="active" href="Registration/register.php"> <i class="fa fa-fw fa-user"></i> Register</a>
    </div>

    <div class="card">
        <div class="registration">
            <div class="center">
                <h1>Registration</h1>
                <?php if (isset($errors['global'])) : ?>
                    <p><span class="error"><?= $errors['global'] ?></span></p>
                <?php endif; ?>
                <br><br><br>
                <form action="" method="post">
                    <div class="inputbox">
                        <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
                        <span>Username:</span>
                        <?php if (isset($errors['username'])) : ?>
                            <span class="error"><?= $errors['username'] ?></span>
                            <br><br><br>

                        <?php endif; ?>
                    </div>

                    <div class="inputbox">
                        <input type="text" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>">
                        <span>Email:</span>
                        <?php if (isset($errors['email'])) : ?>
                            <span class="error"><?= $errors['email'] ?></span>
                            <br><br><br>

                        <?php endif; ?>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="password" id="password">
                        <span>Password:</span>

                        <?php if (isset($errors['password'])) : ?>
                            <span class="error"><?= $errors['password'] ?></span> <br><br><br>

                        <?php endif; ?>
                    </div>
                    <div class="inputbox">
                        <input type="password" name="repassword" id="repassword">
                        <span>Password again:</span>

                    </div>
                    <div class="inputbox">
                        <button type="submit">Register</button>
                    </div>
                </form>
                <section class="goLinks">
                    <a href="../Login/login.php">Login</a>
                    <a href="../index.php">Main page</a>

                </section>
            </div>
        </div>
    </div>

</body>

</html>