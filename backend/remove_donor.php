<?php

// Use PHPMailer library for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'Donors');

// Check if the database connection was successful
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error); // Stop execution if connection fails
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_id = $_POST['donor_id']; // Retrieve the donor ID from the POST request

    // Update the donor's availability in the `donerstable` to 'No' (mark as unavailable)
    $sql = "UPDATE donerstable SET available = 'No' WHERE id = ?";
    $stmt = $conn->prepare($sql); // Prepare the SQL statement
    $stmt->bind_param("i", $donor_id); // Bind the donor ID parameter as an integer

    // Track the number of donations in the session variable `Donated`
    if (!isset($_SESSION['Donated'])) {
        $query = "SELECT COUNT(*) AS count FROM registration WHERE done = 'Yes'";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['Donated'] = $row['count']; // Set the count from the database
        } else {
            $_SESSION['Donated'] = 0; // Default to 0 if no records found
        }
    }
    $_SESSION['Donated']  += 1; // Increment donated count.

    // Execute the update query for the donor
    if ($stmt->execute()) {
        // If the donor update was successful, update the registration entry as done
        $name = $_POST['name']; // Retrieve the donor's name
        $email = $_POST['email']; // Retrieve the donor's email
        $needed_id = $_POST['needed_id']; // Retrieve the related registration ID

        $sql = "UPDATE registration SET done = 'Yes' WHERE id = ?"; // SQL to mark the request as done
        $stmt2 = $conn->prepare($sql); // Prepare the statement
        $stmt2->bind_param("i", $needed_id); // Bind the registration ID parameter as an integer
        $stmt2->execute(); // Execute the update query
        $stmt2->close(); // Close the second prepared statement

        // Initialize PHPMailer to send an email notification to the donor
        $mail = new PHPMailer(true);
        
        // SMTP Configuration for sending the email
        $mail->isSMTP(); // Use SMTP protocol
        $mail->Host = 'smtp.gmail.com'; // SMTP server address
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'maaryu2020@gmail.com'; // Email address used for sending
        $mail->Password = 'vgdctehsjikipflc'; // App-specific password for the email
        $mail->SMTPSecure = 'tls'; // Encryption protocol
        $mail->Port = 587; // SMTP port
        $mail->SMTPDebug = 2; // Debugging level (optional)

        // Configure the email content
        $mail->setFrom('maaryu2020@gmail.com', 'Donor Registration'); // Set the sender
        $mail->addAddress($email, $name); // Add recipient's email and name
        $mail->Subject = 'Your reserved blood waiting for you!'; // Email subject
        $mail->isHTML(true); // Enable HTML formatting for the email
        $mail->Body = "
            Hello $name,<br><br>
            We need you to come to our organization site to receive it <strong>tomorrow</strong>.<br>
            <strong>If you don't come tomorrow, we will cancel it.</strong><br><br>
            Thank you for your understanding. <br>
            See you tomorrow.
        ";
        // Send the email to the donor
        $mail->send();

        // Redirect to the home page with a success message
        header("Location: ../index.html?message=success");
        exit(); // Stop further execution
    } else {
        // Display an error if the donor update fails
        echo "Error removing donor: " . $conn->error;
    }

    // Close the first prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
