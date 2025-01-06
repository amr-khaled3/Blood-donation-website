<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Link to FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Find Donors</title>
    <style>
    /* General Styling */
    body {
        font-family: 'Roboto', sans-serif;
        line-height: 1.6;
        margin: 20px;
        background-color: #f3f4f6;
        color: #333;
    }

    h2 {
        color: rgb(255, 0, 4);
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Styling the Donor List */
    ul {
        list-style-type: none;
        padding: 0;
        max-width: 800px;
        margin: 0 auto;
    }

    li {
        margin: 10px 0;
        padding: 15px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    li:hover {
        transform: translateY(-5px);
        box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* Button Styling */
    button {
        background-color: rgb(255, 0, 8);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    button:hover {
        background-color: rgb(163, 5, 5);
        transform: scale(1.05);
    }

    button:active {
        background-color: rgb(124, 6, 6);
        transform: scale(0.95);
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        li {
            flex-direction: column;
            align-items: flex-start;
        }

        button {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }
    }
    </style>
</head>

<body>
    
    
<?php
// Start the session to maintain state across pages
session_start();

// Use namespaces to include the PHPMailer classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Retrieve form input data from POST request
$name = $_POST['name']; // Name of the user
$email = $_POST['email']; // Email of the user
$blood_type = $_POST['blood_type']; // Blood type needed
$governorate = $_POST['governorate']; // User's governorate (location)
$location = $_POST['location']; // Specific location provided by the user
$age = $_POST['age']; // Age of the user

// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'Donors');

// Check for database connection errors
if ($conn->connect_error)
    die('connection failed : ' . $conn->connect_error);
else {
    // Prepare an SQL statement to insert a new blood request into the `registration` table
    $stmt = $conn->prepare("INSERT INTO registration(name, email, blood_type, governorate, location, age, done)
                            VALUES(?, ?, ?, ?, ?, ?, ?)");
    $done = "No"; // Initially mark the request as not fulfilled
    $stmt->bind_param("sssssis", $name, $email, $blood_type, $governorate, $location, $age, $done);
    $stmt->execute(); // Execute the prepared statement

    // Retrieve the maximum ID from the `registration` table (ID of the latest inserted record)
    $result = $conn->query("SELECT MAX(id) FROM registration");
    $row = $result->fetch_row();
    $needed_id = $row[0]; // Store the latest registration ID

    // Search for available donors in the `donerstable` matching the requested blood type and availability
    $stmt = $conn->prepare("SELECT * FROM donerstable WHERE Blood_Type LIKE ? AND available LIKE ?");
    $bloodSearch = "%" . $blood_type . "%"; // Allow partial matches for blood type
    $availabilitySearch = "Yes"; // Filter for donors who are available
    $stmt->bind_param("ss", $bloodSearch, $availabilitySearch);

    // Execute the search query
    $stmt->execute();
    $result = $stmt->get_result();

    // If there are matching donors
    if ($result->num_rows > 0) {
        echo "<h2>Available blood for you in our organization:</h2>";
        echo "<form method='post' action='remove_donor.php'>"; // Form for selecting a donor
        echo "<ul>";

        // Loop through each matching donor
        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "A (" . $row['Blood_Type'] . ") blood Available at " . $row['governorate'];

            // Display additional information if the donor is on medication
            if ($row['Q2'] == "Yes")
                echo "<br> Currently taking medication, like: " . $row['add_Q2'] . "!!";

            // Display additional information if the donor has health conditions
            if ($row['Q3'] == "Yes")
                echo "<br> Has chronic health conditions or infectious diseases, like: " . $row['add_Q3'] . "!!";

            // Provide a button to select this donor
            echo "<button type='submit' name='donor_id' value='" . $row['id'] . "'>";
            echo "Select <i class='fa fa-check'></i>"; // Visual icon for selection
            echo "<input type='hidden' name='name' value='" . $row['name'] . "'>"; // Pass donor's name
            echo "<input type='hidden' name='email' value='" . $row['email'] . "'>"; // Pass donor's email
            echo "<input type='hidden' name='needed_id' value='" . $needed_id . "'>"; // Pass the request ID
            echo "</button>";
            echo "</li>";
        }

        echo "</ul>";
        echo "</form>"; // Close the form
    } else {
        // If no matching donors are found, redirect to the index page with a message
        header("Location: ../index.html?message=sorry");
    }

    // Close the database connection and prepared statement
    $conn->close();
    $stmt->close();
}
?>

</body>

</html>