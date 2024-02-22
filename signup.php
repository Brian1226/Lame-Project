<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link to Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Sign Up</title>
</head>

<body>
    <?php

    // signup referenced from HW7

    session_start();

    require_once 'db.php';

    $conn = new mysqli($hn, $un, $pw, $db);

    if ($conn->connect_error) {
        die($conn->connect_error);
    }

    // table for users
    $users =
        "
CREATE TABLE IF NOT EXISTS users (
    userID INT AUTO_INCREMENT PRIMARY KEY NOT NULL, 
    username VARCHAR(50) NOT NULL, 
    password VARCHAR(100) NOT NULL)
";

    $usersResult = $conn->query($users);
    if (!$usersResult) {
        die($conn->error);
    }

    // signup functionality
    if (isset($_POST['signup'])) {
        $newUsername = $_POST['newUsername'];
        $newPassword = $_POST['newPassword'];

        $signupSelectQuery = "SELECT * FROM users WHERE username = '$newUsername'";
        $signupSelectResult = $conn->query($signupSelectQuery);

        if ($signupSelectResult->num_rows > 0) {
            echo "<br><h1 style='color:red; text-align: center;'>USERNAME ALREADY EXISTS, CHOOSE ANOTHER ONE</h1><br>";
        } 
        else {
            $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT); // https://www.php.net/manual/en/function.password-hash.php
            $signupInsertQuery = "INSERT INTO users (username, password) VALUES ('$newUsername', '$newPassword')";
            $signupInsertResult = $conn->query($signupInsertQuery);
            echo " <br><h1 style='color:red; text-align: center;'>SIGN UP SUCCESSFUL, NOW LOG IN</h1><br> ";
        }
    }
    ?>

    <script>
        // JS client-side validation from professor's slides
        function validateNewUsername(username) {
            if (username == "") {
                return "No username entered.\n";
            } else {
                return "";
            }
        }

        function validateNewPassword(password) {
            if (password == "") {
                return "No password entered.\n";
            } else if (password.length < 6) {
                return "Password must be at least 6 characters.\n";
            } else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                return "Password must contain at least a lowercase, an uppercase, and a number.\n";
            } else {
                return "";
            }
        }

        function validateSignup(form) {
            fail = validateNewUsername(form.newUsername.value)
            fail += validateNewPassword(form.newPassword.value)

            if (fail == "") {
                return true;
            } else {
                alert(fail);
                return false;
            }
        }
    </script>

    <?php

    echo '<div class="container">';
    echo '<div class="row justify-content-center mt-5">';

    echo '<form class="col-lg-5 col-md-8" action="signup.php" method="POST" onsubmit="return validateSignup(this)" enctype="multipart/form-data">' .
        '<div class="p-5" style="border: 2px solid #0077b6">' .
        '<h2 style="color:#0077b6;">Sign Up</h2>' .
        '<br>' .
        '<label class="mb-2" for="newUsername">New Username</label>' .
        '<br>' .
        '<input class="py-1" type="text" id="newUsername" name="newUsername" style="width: 100%;">' .
        '<br><br>' .
        '<label class="mb-2" for="newPassword">New Password</label>' .
        '<br>' .
        '<input class="py-1" type="password" id="newPassword" name="newPassword" style="width: 100%;">' .
        '<br><br>' .
        '<input class="py-2" type="submit" name="signup" value="SIGN UP" style="width:100%; background:#0077b6; border:none; color:white;">' .
        '<br><br>' .
        '<p>Click <a href="login.php">here</a> to log in </p>' .
        '</div>' .
        '</form>';

    echo '</div>';
    echo '<p class="text-center mt-3"><a href="questions.php">ACCESS QUESTIONS AS A GUEST HERE</a></p>';
    echo '</div>';

    // JS scripts to make Bootstrap functionalities work
    echo '<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
crossorigin="anonymous"></script>';

    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
crossorigin="anonymous"></script>';

    ?>
</body>

</html>