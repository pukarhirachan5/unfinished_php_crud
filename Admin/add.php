<?php
include 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle Image Upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Save product in database
        $sql = "INSERT INTO products (name, description, price, image) VALUES ('$name', '$description', '$price', '$image')";
        if ($conn->query($sql) === TRUE) {
            header("Location: products.php");
            exit();
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "Image upload failed!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
    <h2>Add Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label><input type="text" name="name" required><br>
        <label>Description:</label><textarea name="description" required></textarea><br>
        <label>Price:</label><input type="number" name="price" required><br>
        <label>Image:</label><input type="file" name="image" required><br>
        <button type="submit">Add</button>
    </form>
</body>
</html>
