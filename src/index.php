<?php
include('Storage/storage.php');
include('Auth/auth.php');
include('Storage/userstorage.php');
function redirect($page)
{
    header("Location: $page");
    exit();
}

// input
session_start();
$user_storage = new UserStorage("Storage/DB/users.json");
$auth = new Auth($user_storage);


?>

<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css%22">





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

    <div class=" collapse navbar-collapse ml-auto navbar">
        <a class="active" href="#"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="ActiveForms/activeForms.php"><i class="fa-solid fa-list"></i> Active forms</a>
        <a href="InactiveForms/inActiveForms.php"> <i class="fa-solid fa-list-check"></i> Inactive forms</a>
        <?php
        if (!$auth->is_authenticated()) : ?>
            <a href="Login/login.php "><i class="fa fa-fw fa-user"></i> Login</a>
            <a href="Registration/register.php"> <i class="fa fa-fw fa-user"></i> Register</a>
        <?php endif ?>
        <?php if ($auth->is_authenticated() && $auth->authenticated_user()['role'] === "admin") : ?>
            <a href="PollCreation/pollCreation.php"> <i class="fa-regular fa-square-plus"></i> Poll Creation</a>
        <?php endif ?>
        <?php
        if ($auth->is_authenticated()) : ?>
            <a href="Logout/logout.php"> <i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php endif ?>
    </div>
    <header class="w3-container w3-theme" style="padding:64px 32px">
        <h1 class="w3-xxxlarge" style="margin-left: -20px;">Welcome To The Polls-Questionaries-Voting Platform</h1>
    </header>
    <div id="about-section">
        <div id="about" style="padding-left: 10px;">
            <div id="content" style="display: flex; align-items:center; flex-direction:row">
                <div>
                    <h2>About the polls-voting platform</h2>
                    <p>This platform is used for creating and voting for Polls/Questionaries. a web application where logged-in users can cast their votes on polls (questionnaires/forms).
                        <br>Admin users can create polls for which users can vote by selecting one or more options.
                        On the main page, all polls in the system are listed. Polls that have already<br>ended are at the bottom,
                        while the ongoing polls appear at the top of the page.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="about-section" style="padding-left: 10px;">
        <div id="about">
            <div id="content" style="display: flex; align-items:center; flex-direction:row">
                <div>
                    <h2>List of content: </h2>
                    <ul>
                       <li> <a href="ActiveForms/activeForms.php" style="color: blue;">Active forms</a></li>
                       <li> <a href="InActiveForms/inActiveForms.php" style="color: blue;">Inactive forms</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


</body>

</html>