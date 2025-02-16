<?php
session_start();
include 'config/db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Prevent SQL injection
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='login.html';</script>";
        exit();
    }

    // Fetch user data from database
    $sql = "SELECT id, firstname, lastname, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["firstname"] . " " . $user["lastname"];
            $_SESSION["user_email"] = $user["email"];

            header("Location: index.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="Website Icon" type="png" href="./images/w_logo.png"> 
    <link rel="Website Icon" type="png" href="./images/w_logo.png"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="login.css">
    <title>Irrigation Hub</title>
</head>
<body>
    <div class="parent">
        <div class="child">
            <div class="row r-margin" style="row-gap: 10px;">
                <div class="col-md-6 mt-5">
                    <img src="./images/logo.jpg" alt="imaages" class="bottle">
                    <p class="text-center">Don't have an account? <a href="signup.php">Sign up</a></p>
                </div>
                <div class="col-md-6 mt-5">
                    <h1 class="text-center">Log In</h1>
                    <div>
                    <form class="scale" action="login.php" method="POST">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="text" name="email" placeholder="Email" class="lofo mb-5" required><br>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" class="lofo" required>
                    <button type="submit" class="button">Log In</button>
                    </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>