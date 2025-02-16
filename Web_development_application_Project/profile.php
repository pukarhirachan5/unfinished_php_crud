<?php
session_start();
include 'config/db.php'; // Include database connection

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch the user's data from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $profilePicTmp = $_FILES["profile_picture"]["tmp_name"];
    
    // Handle profile picture upload if a new file is selected
    if (!empty($profilePicTmp)) {
        $profilePicName = $_FILES["profile_picture"]["name"];
        $uploadDir = "uploads/";
        $profilePicPath = $uploadDir . basename($profilePicName);

        // Validate file type
        $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        $fileType = mime_content_type($profilePicTmp);

        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Invalid file type! Please upload JPG, PNG, or GIF.");
        }

        if (!move_uploaded_file($profilePicTmp, $profilePicPath)) {
            die("Error: Failed to upload image.");
        }
    } else {
        // If no file uploaded, keep the old profile picture
        $profilePicPath = $user['profile_picture'];
    }

    // Update user data in the database
    $sql = "UPDATE users SET firstname = ?, lastname = ?, phone = ?, email = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $firstname, $lastname, $phone, $email, $profilePicPath, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}

// Delete user account
if (isset($_POST['delete_account'])) {
    // Delete the user's profile picture if it exists
    if (file_exists($user['profile_picture'])) {
        unlink($user['profile_picture']);
    }

    // Delete the user from the database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        session_destroy();
        header("Location: login.php");
        exit();
    } else {
        echo "Error deleting account: " . $stmt->error;
    }
}

// Logout session
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <title>User Profile</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo $user['firstname']; ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo $user['lastname']; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?php echo $user['phone']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="mt-3" width="100">
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
        </form>

        <form action="profile.php" method="POST" class="mt-4">
            <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
        </form>

        <form action="profile.php" method="POST" class="mt-4">
            <button type="submit" name="logout" class="btn btn-secondary">Logout</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
