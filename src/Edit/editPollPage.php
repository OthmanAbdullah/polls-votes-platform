<?php
include_once("../storage/userstorage.php");
include_once("../storage/storage.php");
include_once("../Auth/auth.php");


// data


// functions
function redirect($page)
{
    header("Location: $page");
    exit();
}
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
function inAnswers($option, &$answers)
{
    for ($i = 0; $i < count($answers); $i++) {
        // if (isset($answers[$i][$option])) {
        // return true;
        // }
    }
    return false;
}

function validate($post, &$data, &$errors)
{
    foreach ($errors as $i => $value) {
        unset($errors[$i]);
    }
    if (!isset($post["question"]) || $post["question"] === "") {
        $errors["question"] = "Invalid question !";
    } else {
        $data["question"] = $post["question"];
    }

    if (!isset($post["deadline"])) {
        $errors["deadline"] = "Deadlien of the form is required!";
    }

    if (validateDate($post["deadline"])) {
        $current_date = date("Y-m-d");
        if ($current_date > $post["deadline"]) {
            $errors["deadline"] =  "current date is greater than deadline date!";
        } else {
            $data["deadline"] = $post["deadline"];
        }
    } else {
        $errors["deadline"] = "Date is not valid (format invalid)";
    }

    if (isset($post["createdAt"]) && $post["createdAt"] !== "") {
        if (validateDate($post["createdAt"])) {
            $data["createdAt"] = $post["createdAt"];
        } else {
            $errors["createdAt"] = "createdAt Date is not valid (format invalid)";
        }
    }

    if (isset($post["isMultiple"]) && ($post["isMultiple"] !== "on")) {
        $errors["isMultiple"] = "isMultiple value is invalid";
    }
    if (isset($post["isMultiple"])  && $post["isMultiple"] == "on") {
        $data["isMultiple"] = "True";
    } else {
        $data["isMultiple"] = "False";
    }
    $options = [];
    $thereIsOptionError = false;
    foreach ($post  as $key => $value) {
        if (!isset($data[$key])) {
            if (trim($value) != "") {
                array_push($options,  $value);
            } else {
                if ($key !== "createdAt" && $key !== "question" && $key !== "deadline") {
                    $thereIsOptionError = true;
                    $errors[$key] = "Empty option is invalid!";
                }
            }
        }
    }
    if (!$thereIsOptionError) {
        if (count($options) < 2) {
            $errors["options count"] = "There have to be at least two choices!";
        } else {
            $data["options"] = $options;
            // redirect("../");
        }
    }
    if (!isset($data["createdAt"])) {
        $data["createdAt"]  =  date('Y-m-d');
    }
    $data["answers"] = [];
    foreach ($options as $option) {
        $obj = (object) array($option => 0);
        array_push($data["answers"],   $obj);
    }
    $data["voted"] = [];
    return count($errors) === 0;
}

function update(&$answers, &$options)
{
    // print_r($answers);

    for ($i = 0; $i < count($answers); $i++) {
        foreach ($answers[$i] as $key => $value) {
            if (!in_array(trim($key), $options)) {
                // var_dump("igen - $key");
                // unset($answers[$i]);
                // unset($answers[$i]);
                // $answers = \array_diff_key($answers, [$key => $value]);

                array_splice($answers, $i, 1);
            }
        }
    }
    print_r($answers);

    foreach ($options as $option) {
        if (!inAnswers($option, $answers)) {
            $obj = (object) array($option => 0);
            array_push ($answers,   $obj ); 
        }
    }
}

// main
session_start();
$user_storage = new UserStorage("../Storage/DB/users.json");
$polls_storage = new UserStorage("../Storage/DB/polls.json");
$auth = new Auth($user_storage);
if (isset($_SESSION["post_data"])) {
    $_POST = $_SESSION["post_data"];
}


// print_r($_POST);

$data = [];
$errors = [];
$poll = $polls_storage->findById($_POST["poll-id"]);
// print_r($poll);
if (count($_POST) > 0) {
    if (!$auth->is_authenticated() && $auth->is_authenticated() && $auth->authenticated_user()['role'] === "admin") {
        redirect('../index.php');
    } else {
        if (isset($_POST["update"])){
            $options = [];
            foreach ($_POST as $key => $value) {
                if ($key != "update" && $key != "question" && $key != "createdAt" && $key != "deadline" && $key != "isMultiple" && $key != "poll-id") {
                    $options[] = $value;
                }
            }
            update($poll["answers"], $options);
            if ($_POST["deadline"] != $poll["deadline"]) {
                $poll["deadline"] = $_POST["deadline"];
            }
            if ($_POST["createdAt"] != $poll["createdAt"]) {
                $poll["createdAt"] = $_POST["createdAt"];
            }
            if ($_POST["isMultiple"] != $poll["isMultiple"]) {
                $poll["createdAt"] = $_POST["isMultiple"];
            }
            $poll["options"] = $options;
            $polls_storage ->update($poll["id"], $poll);   
        }
    }
}
if (isset($_SESSION["post_data"])) {
    unset($_SESSION["post_data"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css%22">

<head>
    <link rel="stylesheet" href="./editPollPage.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Poll</title>
</head>

<body>
    <div>
        <div class=" collapse navbar-collapse ml-auto navbar">
            <a href="../index.php"><i class="fa fa-fw fa-home"></i> Home</a>
            <a href="../ActiveForms/activeForms.php"><i class="fa-solid fa-list"></i> Active forms</a>
            <a href="../InActiveForms/inActiveForms.php"> <i class="fa-solid fa-list-check"></i> Inactive forms</a>
            <?php
            if (!$auth->is_authenticated() || $auth->is_authenticated() && $auth->authenticated_user()['role'] !== "admin") : ?>
                <?php redirect("../index.php") ?>
                <a href="../Login/login.php "><i class="fa fa-fw fa-user"></i> Login</a>
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
        <form action="" method="post" novalidate>
            <input type="text" name="poll-id" value=<?= $poll["id"] ?> hidden>
            <!-- We can use text area, but instead I'll do it with js to make it look fancier, texterea will be much easier tho -->
            <div class="card">
                <h1 class="card-title">Edit Poll</h1>
                <?php if (isset($errors["question"])) : ?>
                    <p><?= $errors["question"] ?></p>
                <?php endif ?>
                <textarea class="question-text" name="question" placeholder="Enter your question here"><?= $poll["question"] ?? "" ?></textarea>
                <div class="card-body">
                    <?php if (isset($errors["options count"])) : ?>
                        <p><?= $errors["options count"] ?></p>
                    <?php endif ?>
                    <?php for ($i = 0; $i < count($poll["options"]); $i++) : ?>
                        <br>
                        <label class="card-choice-textarea">
                            <input type="radio" disabled>
                            <textarea name="choice<?= $i ?>" class="other-choice" placeholder="Enter your question here"><?= $poll["options"][$i] ?? "" ?></textarea>
                            <i class="fas fa-trash"></i>
                        </label>
                    <?php endfor ?>
                    <label class="card-choice-textarea">
                        <br>
                        <a class="add-another-link">+ Add Another</a>
                    </label>
                </div>
                <div class="poll-settings">
                    <label style="display:inline-block;">
                        <input type="checkbox" <?php if (isset($poll["isMultiple"])) : ?> checked <?php endif ?> name="isMultiple"> Allow multiple options to be selected
                    </label>
                    <br>
                    <label style="display:inline-block;">
                        Voting deadline:
                        <input type="date" name="deadline" value=<?= $poll["deadline"] ?? "" ?> required>
                        <?php if (isset($errors["deadline"])) : ?>
                            <p><?= $errors["deadline"] ?></p>
                        <?php endif ?>
                    </label>
                    <br>
                    <label>
                        Time of creation:
                        <input type="date" name="createdAt" value=<?= $poll["createdAt"] ?? "" ?>>
                        <?php if (isset($errors["createdAt"])) : ?>
                            <p><?= $errors["createdAt"] ?></p>
                        <?php endif ?>
                    </label>
                </div>
                <br>
                <input type="submit" value="Update" name="update">
            </div>
        </form>
    </div>
    <script src="AddChoice.js"></script>
</body>

</html>