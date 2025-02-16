<?php
include 'db.php';
$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: products.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>
    <form method="POST">
        <label>Name:</label><input type="text" name="name" value="<?= $product['name'] ?>" required><br>
        <label>Description:</label><textarea name="description" required><?= $product['description'] ?></textarea><br>
        <label>Price:</label><input type="number" name="price" value="<?= $product['price'] ?>" required><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
