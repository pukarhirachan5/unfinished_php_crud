<?php
include 'config/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $uploadDir = "uploads/";

    // Validate passwords
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match!");
    }

    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Handle profile picture upload
    $profilePicName = $_FILES["profile_picture"]["name"];
    $profilePicTmp = $_FILES["profile_picture"]["tmp_name"];
    $profilePicPath = $uploadDir . basename($profilePicName);

    $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    $fileType = mime_content_type($profilePicTmp);

    if (!in_array($fileType, $allowedTypes)) {
        die("Error: Invalid file type! Please upload JPG, PNG, or GIF.");
    }

    if (!move_uploaded_file($profilePicTmp, $profilePicPath)) {
        die("Error: Failed to upload image.");
    }

    // Insert user into database
    $sql = "INSERT INTO users (firstname, lastname, phone, email, profile_picture, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $firstname, $lastname, $phone, $email, $profilePicPath, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <link rel="Website Icon" type="png" href="../images/w_logo.png"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="login.css">
    <title>Irrigation Hub</title>
</head>
<body>
    <div class="parent">
        <div class="child">
            <div class="row r-margin" style="row-gap: 10px;">
                <div class="col-md-6 mt-5">
                    <img src="./images/logo.jpg" alt="images" class="bottle">
                    <p class="text-center">Already have an account? <a href="login.php">Login</a></p>
                </div>
                <div class="col-md-6 mt-5">
                    <h1 class="text-center">Sign Up</h1>
                    <div>
                        <form class="scale" action="signup.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="firstname" id="firstname" placeholder="First Name" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="lastname" id="lastname" placeholder="Last Name" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="phone" id="phone" placeholder="Phone Number" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" id="email" placeholder="Email" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-image"></i>
                            <input type="file" name="profile_picture" id="profile_picture" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Password" class="lofo mb-3" required><br>
                            
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="lofo mb-3" required><br>
                            
                            <button type="submit" class="button">Sign up</button>
                        </form>
                        
                        <script>
                        function validateForm() {
                            let firstname = document.getElementById("firstname").value.trim();
                            let lastname = document.getElementById("lastname").value.trim();
                            let phone = document.getElementById("phone").value.trim();
                            let email = document.getElementById("email").value.trim();
                            let profilePicture = document.getElementById("profile_picture").value;
                            let password = document.getElementById("password").value;
                            let confirmPassword = document.getElementById("confirm_password").value;
                        
                            // Name validation (at least 2 characters)
                            if (firstname.length < 2 || lastname.length < 2) {
                                alert("First and Last Name must be at least 2 characters.");
                                return false;
                            }
                        
                            // Phone number validation (digits only, 10-15 characters)
                            // let phonePattern = /^{10}$/;
                            // if (!phonePattern.test(phone)) {
                            //     alert("Enter a valid phone number (10-15 digits).");
                            //     return false;
                            // }
                        
                            // Email validation
                            let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                            if (!emailPattern.test(email)) {
                                alert("Enter a valid email address.");
                                return false;
                            }
                        
                            // Profile picture validation (only JPG, PNG, GIF)
                            let allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                            if (!allowedExtensions.test(profilePicture)) {
                                alert("Please upload a valid image file (JPG, PNG, GIF).");
                                return false;
                            }
                        
                            // Password validation (at least 6 characters, one number, one special character)
                            let passwordPattern = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,}$/;
                            if (!passwordPattern.test(password)) {
                                alert("Password must be at least 6 characters long and include at least one number and one special character.");
                                return false;
                            }
                        
                            // Confirm password validation
                            if (password !== confirmPassword) {
                                alert("Passwords do not match.");
                                return false;
                            }
                        
                            return true; // If all validations pass, submit the form
                        }
                        </script>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
