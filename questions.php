<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link to Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Questions</title>

</head>

<body>

</body>

</html>

<?php
session_start();

require_once 'db.php';

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die($conn->connect_error);
}

// table for questions
$questions =
    "
CREATE TABLE IF NOT EXISTS questions (
    userID INT, 
    question VARCHAR(256), 
    FOREIGN KEY (userID) REFERENCES users(userID))
";

$questionsResult = $conn->query($questions);
if (!$questionsResult) {
    die($conn->error);
}

// function to read each line of the file
function getFileContent()
{

    if (isset($_FILES["file"]) && !empty($_FILES["file"]["name"])) # https://www.php.net/manual/en/features.file-upload.post-method.php
    {
        $f = $_FILES["file"]["name"];
        $fh = fopen($f, 'r') or die("<br>File does not exist or you lack permission to open it!<br>");
        $fe = pathinfo($f, PATHINFO_EXTENSION); # https://www.php.net/manual/en/function.pathinfo.php

        if ($fe != "txt") {
            die("<br>Must be a '.txt' file!<br>");
        }

        $fileContent = "";
        while (!feof($fh)) {
            $line = fgets($fh);
            $fileContent .= $line;
        }
        fclose($fh);
        return $fileContent;
    }
    return NULL;
}

// Questions page for login users
if (isset($_SESSION['username'])) {
    echo '<div class="container-fluid">';
    echo '<div class="row justify-content-around py-3">';

    echo '<div class="col-md-6">';
    echo ' <h1> Welcome, ' . $_SESSION['username'] . '</h1>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<form action="questions.php" method="POST">';
    echo '<input class="col-md-6 py-3" type="submit" name="logout" value="Log Out" style="background:#0077b6; border:none; color: white;">';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<form class="mb-5" action="questions.php" method="POST" enctype="multipart/form-data" style="border: 1px solid black; padding: 10px; margin-bottom: 10px;">';
    echo '<label for="file">Upload a file:</label>';
    echo '<input type="file" name="file" id="file">';
    echo '<br><br>';
    echo '<input type="hidden" name="username" value="' . $_SESSION['username'] . '">';
    echo '<input type="submit" name="submit" value="Submit">';
    echo '</form>';

    echo '<form class="mb-5 text-center" action="questions.php" method="POST" enctype="multipart/form-data" style="border: 1px solid black; padding: 10px; margin-bottom: 10px;">';
    echo '<div class="pt-3"><h2>Question</h2>';
    echo '<input type="hidden" name="username" value="' . $_SESSION['username'] . '">';
    echo '<input class="py-2 px-3 my-3" type="submit" name="randomize" value="Randomize" style="background:#0077b6; border:none; color: white;">';
    echo '</form>';

    //submit functionality 
    if (isset($_POST['submit'])) {
        $fileContent = getFileContent();
        $username = $_POST['username'];

        // error message for no file submitted
        if ($fileContent == NULL) {
            die('<br>Please input a file!<br>');
        } 
        // submit the questions
        else {
            $userQuery = "SELECT userID FROM users WHERE username='$username'";
            $userResult = $conn->query($userQuery);

            if (!$userResult) {
                die($conn->error);
            }

            $userRow = $userResult->fetch_array(MYSQLI_ASSOC);
            $userID = $userRow['userID'];

            // split the string using the new line delimiter, into an array of questions
            $questions = explode("\n", $fileContent); // https://www.php.net/manual/en/function.explode.php

            // insert each individual questions (that was separated by new line from the file) into db as their own row
            foreach ($questions as $question) {
                $insertQuery = "INSERT INTO questions (userID, question) VALUES ('$userID', '$question')";
                $insertResult = $conn->query($insertQuery);

                if (!$insertResult) {
                    die($conn->error);
                }
            }
            echo '<p>Questions from the file submitted.</p>';
        }
    }

    // randomize functionality
    if (isset($_POST['randomize'])) {
        $username = $_POST['username'];

        $userQuery = "SELECT userID FROM users WHERE username='$username'";
        $userResult = $conn->query($userQuery);

        if (!$userResult) {
            die($conn->error);
        }

        $userRow = $userResult->fetch_array(MYSQLI_ASSOC);
        $userID = $userRow['userID'];

        $selectQuery = "SELECT question FROM questions WHERE userID='$userID'";
        $selectResult = $conn->query($selectQuery);
        $rows = $selectResult->num_rows;

        // if there's questions in the db for that users, randomize that, otherwise randomize the default questions
        if ($rows > 0) {
            $randomIndex = rand(0, $rows); // https://www.php.net/manual/en/function.rand.php
            $selectResult->data_seek($randomIndex);
            $row = $selectResult->fetch_array(MYSQLI_ASSOC);
            $question = $row['question'];
            echo "<h5>" . $question . "</h5>";
        } else {
            $defaultQuestions = ["Dog or cat?", "What's your favorite food?", "How old are you?", "Where are you from?", "What's 1 + 1?"];
            $randomIndex = rand(0, count($defaultQuestions) - 1); // https://www.php.net/manual/en/function.rand.php
            echo "<h5>" . $defaultQuestions[$randomIndex] . "</h5>";
        }
    }

    echo '</div>';
    echo '</div>';
}
// questions page for guests
else {
    echo '<div class="container-fluid mt-3">';
    echo '<div class="row justify-content-around py-3">';

    echo '<div class="col-md-6">';
    echo ' <h1> Welcome, Guest</h1>';
    echo '</div>';

    echo '<div class="col-md-3">';
    echo '<form action="questions.php" method="POST">';
    echo '<a class="col-md-6 px-5 py-3" href="login.php" style="background:#0077b6; border:none; color: white; text-decoration: none;"> Log In</a>';
    echo '</form>';
    echo '</div>';
    echo '</div>';

    echo '<form class="mb-5 text-center" action="questions.php" method="POST" enctype="multipart/form-data" style="border: 1px solid black; padding: 10px; margin-bottom: 10px;">';
    echo '<div class="pt-3"><h2>Question</h2>';
    echo '<input class="py-2 px-3 my-3" type="submit" name="randomize" value="Randomize" style="background:#0077b6; border:none; color: white;">';
    echo '</form>';

    // randomize functionality
    if (isset($_POST['randomize'])) {
        $defaultQuestions = ["Dog or cat?", "What's your favorite food?", "How old are you?", "Where are you from?", "What's 1 + 1?"];
        $randomIndex = rand(0, count($defaultQuestions) - 1); // https://www.php.net/manual/en/function.rand.php
        echo "<h5>" . $defaultQuestions[$randomIndex] . "</h5>";
    }

    echo '</div>';
    echo '</div>';
}

// log out functionality, referenced from professor's slides
if (isset($_POST['logout'])) {
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
    header("Location: login.php");
    $conn->close();
}
$conn->close();

// JS scripts to make Bootstrap functionalities work
echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
    crossorigin="anonymous"></script>';

echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
    crossorigin="anonymous"></script>';

?>