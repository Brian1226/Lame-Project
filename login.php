<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link to Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Login</title>
</head>

<body>

    <?php

    // login referenced from HW7

    session_start();

    require_once 'db.php';

    $conn = new mysqli($hn, $un, $pw, $db);

    if ($conn->connect_error) {
        die($conn->connect_error);
    }

    $validUser = False;

    // login functionality 
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $loginQuery = "SELECT * FROM users where username = '$username'";
        $loginResult = $conn->query($loginQuery);

        if (!$loginResult) {
            die($conn->error);
        }

        $userRow = $loginResult->fetch_array(MYSQLI_ASSOC);
        if (isset($userRow['password']) && password_verify($password, $userRow['password'])) { // https://www.php.net/manual/en/function.password-verify.php
            $validUser = True;
            $_SESSION['username'] = $username;
        } else {
            echo "<br><h1 style='color:red; text-align: center;'>WRONG USERNAME / PASSWORD</h1><br>";
        }
    }
    ?>

    <script>
        // JS client-side validation from professor's slides
        function validateUsername(username) {
            if (username == "") {
                return "No username entered.\n";
            } else {
                return "";
            }
        }

        function validatePassword(password) {
            if (password == "") {
                return "No password entered.\n";
            } else {
                return "";
            }
        }

        function validateLogin(form) {
            fail = validateUsername(form.username.value)
            fail += validatePassword(form.password.value)

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

    echo '<form class="col-lg-5 col-md-8" action="login.php" method="POST" onsubmit="return validateLogin(this)" enctype="multipart/form-data">' .
    '<div class="p-5" style="border: 2px solid #0077b6">' .
        '<h2 style="color:#0077b6;">Login</h2>' .
        '<br>' .
        '<label class="mb-2" for="username">Username</label>' .
        '<br>' .
        '<input class="py-1" type="text" id="username" name="username" style="width: 100%;">' .
        '<br><br>' .
        '<label class="mb-2" for="password">Password</label>' .
        '<br>' .
        '<input class="py-1" type="password" id="password" name="password" style="width: 100%;">' .
        '<br><br>' .
        '<input class="py-2" type="submit" name="login" value="LOG IN" style="width:100%; background:#0077b6; border:none; color: white;">' .
        '<br><br>' .
        '<p>Click <a href="signup.php">here</a> to sign up </p>' .
        '</div>' .
        '</form>';

    echo '</div>';
    echo '<p class="text-center mt-3"><a href="questions.php">ACCESS QUESTIONS AS A GUEST HERE</a></p>';
    echo '</div>';

    // if entered valid credentials, then redirect to the questions page
    if (isset($_SESSION['username']) && $validUser) {
        header("Location: questions.php");
        $conn->close();
    }

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