<?php
    ob_start(); // Start output buffering to allow header redirects later.
    session_start(); // Start the session to store session variables.

    // Include namespaces for PHPMailer classes.
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Include PHPMailer files for sending emails.
    require '../phpmailer/src/Exception.php';
    require '../phpmailer/src/PHPMailer.php';
    require '../phpmailer/src/SMTP.php';

    // Collect form data from the POST request.
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $Gender = $_POST['Gender']; // Options: m (male), f (female), o (other)
    $governorate = $_POST['governorate'];
    $donation_type = $_POST['donation_type'];
    $Blood_Type = $_POST['Blood_Type'];
    $Q1 = $_POST['Q1']; // Question: Was the last time you donated blood four months ago or more?
    $Q2 = $_POST['Q2']; // Question: Are you currently on any medication?
    $add_Q2 = $_POST['add_Q2']; // Additional input: What medication are you taking?
    $Q3 = $_POST['Q3']; // Question: Do you have any chronic health conditions or infectious diseases?
    $add_Q3 = $_POST['add_Q3']; // Additional input: What health conditions or diseases do you have?

    // Establish a database connection.
    $conn = new mysqli('localhost', 'root', '', 'Donors');
    if ($conn->connect_error)
        die('Connection failed: ' . $conn->connect_error);
    else {
        // Create a new PHPMailer instance.
        $mail = new PHPMailer(true);

        // Configure SMTP settings for sending email.
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server address.
        $mail->SMTPAuth = true; // Enable SMTP authentication.
        $mail->Username = 'maaryu2020@gmail.com'; // Sender email address.
        $mail->Password = 'vgdctehsjikipflc'; // Sender email password (should be stored securely).
        $mail->SMTPSecure = 'tls'; // Encryption type (TLS).
        $mail->Port = 587; // SMTP port number.
        $mail->SMTPDebug = 2; // Enable debug output for troubleshooting (optional).

        // Set up the email content.
        $mail->setFrom('maaryu2020@gmail.com', 'Donor Registration'); // Sender information.
        $mail->addAddress($email, $name); // Recipient information.
        $mail->Subject = 'About your donation!'; // Email subject.

        // Check if the donor is eligible to donate based on Q1 response.
        if ($Q1 == "Yes") {
            $available = "Yes"; // Donor is available for donation.

            // Prepare and execute a SQL query to insert donor details into the database.
            $stmt = $conn->prepare("INSERT INTO donerstable(name, email, age, Gender, Blood_Type, governorate, donation_type, Q1, Q2, add_Q2, Q3, add_Q3, phone, available) 
                                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssssssssss", $name, $email, $age, $Gender, $Blood_Type, $governorate, $donation_type, $Q1, $Q2, $add_Q2, $Q3, $add_Q3, $phone, $available);
            $stmt->execute(); // Execute the query.
            $stmt->close(); // Close the statement.

            // Retrieve the max donor ID for session tracking.
            $result = $conn->query("SELECT MAX(id) FROM donerstable");
            $row = $result->fetch_row();
            $donor_id = $row[0]; // Extract the max ID.
            $_SESSION['donor_id'] = $donor_id; // Store the donor ID in the session.

            // Initialize or update session variables for statistics.
            
            // Initialize Blood_Group_Distribution dynamically from the database.
            if (!isset($_SESSION['Blood_Group_Distribution'])) {
                $_SESSION['Blood_Group_Distribution'] = [];
                // Queries the database to count the number of donors for each blood type
                $query = "SELECT Blood_Type, COUNT(*) AS count FROM donerstable GROUP BY Blood_Type";
                $result = $conn->query($query);

                $bloodTypes = ["A+", "B+", "AB+", "O+", "A-", "B-", "AB-", "O-"];
                foreach ($bloodTypes as $type) {
                    $_SESSION['Blood_Group_Distribution'][$type] = 0; // Default to 0 if no entries.
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $_SESSION['Blood_Group_Distribution'][$row['Blood_Type']] = $row['count'];
                    }
                }
            }

            $_SESSION['Blood_Group_Distribution'][$Blood_Type] += 1; // Increment blood group count.

            // Initialize Donors_by_Age_Group dynamically from the database.
            if (!isset($_SESSION['Donors_by_Age_Group'])) {
                $_SESSION['Donors_by_Age_Group'] = [
                    "18to25" => 0,
                    "26to35" => 0,
                    "36to45" => 0,
                    "46to55" => 0,
                    "56+" => 0,
                ];
                //Fetches all ages from the database.
                $query = "SELECT age FROM donerstable";
                $result = $conn->query($query);
                // Iterates through each donor's age to categorize them into the predefined age groups.
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $age = $row['age'];
                        if ($age < 26) {
                            $_SESSION['Donors_by_Age_Group']['18to25']++;
                        } elseif ($age < 36) {
                            $_SESSION['Donors_by_Age_Group']['26to35']++;
                        } elseif ($age < 46) {
                            $_SESSION['Donors_by_Age_Group']['36to45']++;
                        } elseif ($age < 56) {
                            $_SESSION['Donors_by_Age_Group']['46to55']++;
                        } else {
                            $_SESSION['Donors_by_Age_Group']['56+']++;
                        }
                    }
                }
            }

            // Increment the appropriate age group count.
            $_SESSION['Donors_by_Age_Group']['18to25'] += ($age < 26);
            $_SESSION['Donors_by_Age_Group']['26to35'] += ($age < 36 && $age > 25);
            $_SESSION['Donors_by_Age_Group']['36to45'] += ($age < 46 && $age > 35);
            $_SESSION['Donors_by_Age_Group']['46to55'] += ($age < 56 && $age > 45);
            $_SESSION['Donors_by_Age_Group']['56+'] += ($age > 55);

            // Initialize recent_donors dynamically from the database.
            if (!isset($_SESSION['recent_donors'])) {
                $_SESSION['recent_donors'] = [];
                //Fetches the last 3 entries from the database (ORDER BY id DESC LIMIT 3) to populate recent donor data.
                $query = "SELECT name, governorate, Blood_Type FROM donerstable ORDER BY id DESC LIMIT 3";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $_SESSION['recent_donors'][] = [
                            'name' => $row['name'],
                            'location' => $row['governorate'],
                            'Blood_Type' => $row['Blood_Type'],
                        ];
                    }
                }
            }
            // Add donor details to the recent donors session variable.
            $newDonor = [
                'name' => $name,
                'location' => $governorate,
                'Blood_Type' => $Blood_Type,
            ];
            array_unshift($_SESSION['recent_donors'], $newDonor); // Add new donor to the beginning.
            $_SESSION['recent_donors'] = array_slice($_SESSION['recent_donors'], 0, 3); // Keep only the last 3 donors.
            


            // Check for potential matches in the database.
            $stmt = $conn->prepare("SELECT * FROM registration WHERE blood_type LIKE ? AND done LIKE ?");
            $searchTerm = "%" . $Blood_Type . "%"; // Partial match for blood type.
            $done = "No"; // Only consider donors who haven't completed the process.
            $stmt->bind_param("ss", $searchTerm, $done);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) { // If matches are found.
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

                // Mark the current donor as unavailable.
                $sql = "UPDATE donerstable SET available = 'No' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $donor_id);
                $stmt->execute();
                $stmt->close();

                // Notify the earliest matching donor.
                $donor = $result->fetch_assoc();
                $recipientEmail = $donor['email'];
                $recipientName = $donor['name'];
                $donorId = $donor['id'];

                $mail2 = new PHPMailer(true); // Create a new mail instance for the recipient.
                $mail2->isSMTP();
                $mail2->Host = 'smtp.gmail.com';
                $mail2->SMTPAuth = true;
                $mail2->Username = 'maaryu2020@gmail.com';
                $mail2->Password = 'vgdctehsjikipflc';
                $mail2->SMTPSecure = 'tls';
                $mail2->Port = 587;
                $mail2->SMTPDebug = 2;

                // Configure email to the recipient.
                $mail2->setFrom('maaryu2020@gmail.com', 'Donor Registration');
                $mail2->addAddress($recipientEmail, $recipientName);
                $mail2->Subject = 'Your blood type is available!';
                $mail2->isHTML(true); // Enable HTML formatting for the email
                $mail2->Body = "
                    Hello $recipientName,<br><br>
                    <strong>Your blood type is available now.</strong><br>
                    Please visit our site <strong>tomorrow</strong> to receive it.<br>
                    <strong>If you don't, the reservation will be canceled.</strong><br><br>
                    Thank you!
                ";
                $mail2->send();

                // Mark the registration as completed.
                $sql = "UPDATE registration SET done = 'Yes' WHERE id = ?";
                $stmt2 = $conn->prepare($sql);
                $stmt2->bind_param("i", $donorId);
                $stmt2->execute();
                $stmt2->close();
            }

            // Email content for eligible donors.
            $mail->isHTML(true); // Enable HTML formatting for the email
            $mail->Body = "
                Hello $name,<br><br>
                Thank you for registering as a donor.<br>
                <strong>Details:</strong><br>
                Blood Type: <strong>$Blood_Type</strong><br>
                Donation Type: <strong>$donation_type</strong><br>
                Governorate: <strong>$governorate</strong><br><br>
                We look forward to your donation <strong>tomorrow</strong>.
            ";
        } else {
            // Email content for ineligible donors.
            $mail->isHTML(true);
            $mail->Body = "
                Hello $name,<br><br>
                We appreciate your willingness to donate blood.<br>
                <strong>However, you are not eligible at this time</strong> as it hasn't been four months since your last donation.<br><br>
                Thank you for your understanding.
            ";
        }

        $conn->close(); // Close the database connection.
        $mail->send(); // Send the email.
        header("Location: ../index.html?message=success1"); // Redirect with a success message.
        ob_end_flush(); // End output buffering.
        exit(); // Terminate the script.
    }
?>
