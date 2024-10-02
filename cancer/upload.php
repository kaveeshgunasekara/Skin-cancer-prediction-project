<?php
// Database connection details
$servername = "localhost"; // Change if necessary
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "skin_cancer_prediction"; // Your database name

// Create connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to save uploaded image and return its absolute path
function saveImage($image) {
    $target_dir = "uploads/";

    // Ensure directory exists or create it
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            die("Failed to create upload directory.");
        }
    }

    // Sanitize the file name to prevent directory traversal attacks
    $image_name = basename($image["name"]);
    $image_name = preg_replace("/[^a-zA-Z0-9.]/", "_", $image_name); // Replace special characters
    $target_file = $target_dir . $image_name;

    // Check if the file is an actual image
    $check = getimagesize($image["tmp_name"]);
    if ($check !== false) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Return the absolute path
            return realpath($target_file);
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } else {
        echo "File is not an image.";
        exit();
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Save image and get its absolute path
    if (isset($_FILES["skinImage"]) && $_FILES["skinImage"]["error"] == 0) {
        $image_path = saveImage($_FILES["skinImage"]);
    } else {
        echo "Error: No file was uploaded.";
        exit();
    }

    // Get optional parameters from form
    $learning_rate = isset($_POST['learningRate']) ? floatval($_POST['learningRate']) : null;
    $num_epochs = isset($_POST['numEpochs']) ? intval($_POST['numEpochs']) : null;
    $batch_size = isset($_POST['batchSize']) ? intval($_POST['batchSize']) : null;

    // Prepare SQL statement to insert data
    $stmt = $conn->prepare("INSERT INTO predictions (image_path, learning_rate, num_epochs, batch_size) VALUES (?, ?, ?, ?)");
    // Note: "d" is used for float, "i" for integer
    $stmt->bind_param("sdii", $image_path, $learning_rate, $num_epochs, $batch_size);

    // Execute SQL statement
    if ($stmt->execute()) {
        echo "Data and image successfully saved!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
}

$conn->close();
?>
