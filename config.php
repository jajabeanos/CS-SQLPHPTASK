<?php
// Database connection
$conn = mysqli_init();
$conn->real_connect("127.0.0.1", "root", "", "vacation_reviews", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $destination = htmlspecialchars(trim($_POST['destination']));
    $review = htmlspecialchars(trim($_POST['review']));
    $rating = intval($_POST['rating']);
    
    // Handle file upload
    $picture_path = null;
    if (isset($_FILES['pictures']) && $_FILES['pictures']['error'] == 0) {
        $target_dir = "uploads/";
        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, recursive: true);
        }
        
        // Sanitize filename and add timestamp
        $safe_filename = preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['pictures']['name']);
        $target_file = $target_dir . time() . '_' . $safe_filename;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['pictures']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['pictures']['tmp_name'], $target_file)) {
                $picture_path = $target_file;
            }
        }
    }
    
    // Validate required fields
    if (empty($username) || empty($destination) || empty($review) || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields and provide a valid rating']);
        exit();
    }
    
    // Insert into database
    $sql = "INSERT INTO submissions (username, destination, pictures, review, rating) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $destination, $pictures, $review, $rating);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    
    $stmt->close();
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Vacation Review Form</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <header>
        <h1>Review Your Past Vacation</h1>
    </header>
    <main>
        <form method="POST" action="config.php" enctype="multipart/form-data" id="reviewForm">
            <label for="username">Your Name:</label>
            <input type="text" name="username" id="username" required>

            <label for="destination">Name of Destination:</label>
            <input type="text" name="destination" id="destination" required>

            <hr>

            <label for="pictures">Pictures of Destination:</label>
            <input type="file" name="pictures" id="pictures" accept="image/*">

            <label for="review">Review:</label>
            <textarea id="review" name="review" placeholder="Enter your comments..." required></textarea>

            <label for="starRating">Rating:</label>
            <div class="starRating">
                <button type="button" class="star" id="1">&#9734;</button>
                <button type="button" class="star" id="2">&#9734;</button>
                <button type="button" class="star" id="3">&#9734;</button>
                <button type="button" class="star" id="4">&#9734;</button>
                <button type="button" class="star" id="5">&#9734;</button>
            </div>
            <input type="hidden" id="starRatingValue" name="rating" value="0" required>

            <button type="submit" id="submit">Submit</button>
        </form>
    </main>
    <script src="script.js"></script>
</body>

</html>