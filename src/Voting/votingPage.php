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

function validate($poll, $post, &$data, &$errors)
{
    if ($poll == null) {
        $errors["invalid-id"] = "Given poll id is invalid!";
    }
    if (isset($post["voted"])) {
        $flag = false;
        foreach ($post as $key => $value) {
            if ($key != "voted" && $key != "poll-id") {
                if (in_array($value, $poll["options"])) {
                    $flag = true;
                    break;
                }
            }
        }
        if (!$flag) {
            $errors["invalid-option"] = "Please select option/s";
        }
    }
    $data = $post;
    return count($errors) === 0;
}
function incAnswer(&$answers, $value)
{
    for ($i = 0; $i < count($answers); $i++) {
        if (isset($answers[$i][$value])) {
            $answers[$i][$value] += 1;
        }
    }
}

// main
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$auth = new Auth($user_storage);
$data = [];
$errors = [];
$voted_successfully = false;
if(isset( $_SESSION["post_data"])){
    $_POST = $_SESSION["post_data"];
}
if (count($_POST) > 0) {
    $poll = $polls_storage->findById($_POST["poll-id"]);
    if (validate($poll, $_POST, $data, $errors)) {
        $voted_successfully = false;
        if (!$auth->is_authenticated()) {
            redirect('../Login/login.php');
        } else {
            if (isset($data["voted"])) {
                print_r($_POST);
                foreach ($data as $key => $value) {
                    if ($key != "voted" && $key != "poll-id") {
                        incAnswer($poll["answers"], $value);
                    }
                }
                array_push($poll["voted"], $auth->authenticated_user()['username']);
                $polls_storage->update($poll["id"], $poll);
                $voted_successfully = true;
            }
        }
    }
    unset($_SESSION["post_data"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="votingPage.css">
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
    <title>Document</title>
</head>

<body>

    <div class=" collapse navbar-collapse ml-auto navbar">
        <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a class="active" href="../ActiveForms/activeForms.php"><i class="fa-solid fa-list"></i> Active forms</a>
        <a href="../InActiveForms/inActiveForms.php"> <i class="fa-solid fa-list-check"></i> Inactive forms</a>
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
        <h1 class="card-title">
            Active Poll
            <div class="poll-date">Created: <span><?= $poll["createdAt"] ?></span></div>
            <div class="poll-deadline">Deadline: <span><?= $poll["deadline"] ?></span></div>
        </h1>

        <?php if (isset($errors["invalid-option"])) : ?>
            <p style="color:red;"><?= $errors["invalid-option"] ?></p>
        <?php endif ?>
        <?php if ($voted_successfully) : ?>
            <p style="color:green;">Successfully voted</p>
        <?php endif ?>
        <div class="poll-description">
            <p><?= $poll["question"] ?></p>
        </div>
        <form action="" method="post">
            <div class="poll-options">
                <input type="text" name="voted" value="true" hidden>
                <?php if ($poll["isMultiple"] == "True") : ?>
                    <input type="text" name="poll-id" value="<?= $poll["id"] ?>" hidden>
                    <?php for ($i = 0; $i < count($poll["options"]); $i++) : ?>
                        <div class="poll-option option-card">
                            <input type="checkbox" name="choice<?= $i ?>" value= '<?= $poll["options"][$i] ?>' <?php if (isset($_POST["choice$i"])) : ?> checked <?php endif ?>
                                <?php if ($voted_successfully) : ?>
                                    disabled
                                <?php endif?>
                            >
                            <label for="option1"><?= $poll["options"][$i] ?></label>
                        </div>
                    <?php endfor ?>
                <?php endif ?>
                <?php if ($poll["isMultiple"] == "False") : ?>
                    <input type="text" name="poll-id" value="<?= $poll["id"] ?>" hidden>
                    <?php for ($i = 0; $i < count($poll["options"]); $i++) : ?>
                        <div class="poll-option option-card">
                            <input type="radio" name="choice" value='<?= $poll["options"][$i] ?>'
                            <?php if (isset($_POST["choice"])) : ?> checked <?php endif ?>
                                <?php if ($voted_successfully) : ?>
                                    disabled
                                <?php endif?>
                                >
                            <label for="option1"><?= $poll["options"][$i] ?></label>
                        </div>
                    <?php endfor ?>
                <?php endif ?>
            </div>
            <?php if (!$voted_successfully) : ?>
                <div class="poll-submit-wrap">
                    <button class="poll-submit" type="submit">Submit</button>
                </div>
            <?php endif ?>
        </form>
        <?php if ($voted_successfully) : ?>
            <div class="poll-submit-wrap">
                <a href="../ActiveForms/activeForms.php"><button class="poll-submit">Back</button></a>
            </div>
        <?php endif ?>
    </div>

</body>

</html>