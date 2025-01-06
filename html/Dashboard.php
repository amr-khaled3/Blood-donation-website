<?php
// Start a session to store and retrieve data across multiple pages
session_start(); 

// Create a new connection to the MySQL database 'Donors' using localhost, root user, and no password
$conn = new mysqli('localhost', 'root', '', 'Donors'); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"> <!-- Specifies the character encoding for the HTML document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensures proper scaling on mobile devices -->
    <!-- Include the Chart.js library for data visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <!-- Link to external CSS for dashboard styling -->
    <link rel="stylesheet" href="style/dashboard.css">
    <!-- Set the favicon of the page -->
    <link rel="icon" type="image/x-icon" href="images/GBF-icon-gbf.png">

    <title>BloodDrop</title> <!-- Set the page title -->
</head>

<body>
    <header class = "header">
        <!-- Logo and title linking to the homepage -->
        <a href="index.html" class="logo"><img src="./images/logo.jpg">
            <h2>bloodDrop</h2>
        </a>
        <nav>
            <!-- Navigation menu with links to various sections -->
            <ul class="navigation">
                <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="donate.html" class="nav-link">Donate blood</a></li>
                <li class="nav-item"><a href="FindDonors.html" class="nav-link">Find donors</a></li>
                <li class="nav-item"><a href="Dashboard.php" class="nav-link active">Dashboard</a></li>
                <li class="nav-item"><a href="aboutUs.html" class="nav-link">About us</a></li>
                <li class="nav-item"><a href="index.html#contactUs" class="nav-link">Contact us</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard">
        <main class="content">
            <header>
                <h1>Blood Donation Statistics</h1>
                <p>
                    <?php echo date('d F Y'); ?> <!-- Display the current date -->
                </p>
            </header>
            <section class="summary">
                <div class="card visitors">
                    <h3>Total Donated blood</h3>
                    <p><?php
                        // Check if 'Donated' is already set in the session
                        if (!isset($_SESSION['Donated'])) {
                            // Query to count the total number of successful blood donations
                            $query = "SELECT COUNT(*) AS count FROM registration WHERE done = 'Yes'";
                            $result = $conn->query($query);
                        
                            if ($result->num_rows > 0) {
                                // Fetch the count and store it in the session
                                $row = $result->fetch_assoc();
                                $_SESSION['Donated'] = $row['count'];
                            } else {
                                $_SESSION['Donated'] = 0; // Default to 0 if no records found
                            }
                        } 
                        // Output the total donated blood count
                        echo htmlspecialchars($_SESSION['Donated']);
                        ?></p>
                </div>
                <div class="card donors">
                    <h3>Total Donors</h3>
                    <p id="total_donors">
                        <?php
                        // Check if 'donor_id' is already set in the session
                        if (!isset($_SESSION['donor_id'])) {
                            // Query to get the maximum donor ID (assumed to be the total donors)
                            $result = $conn->query("SELECT MAX(id) FROM donerstable");
                            $row = $result->fetch_row();
                            $donor_id = $row[0]; // Extract the max ID.
                            $_SESSION['donor_id'] = $donor_id; // Store the donor ID in the session.
                        } 
                        // Output the total donors
                        echo htmlspecialchars($_SESSION['donor_id']);
                        ?>
                    </p>
                </div>
            </section>
            <section class="charts">
                <div class="chart">
                    <h3>Donors by Age Group</h3>
                    <!-- Placeholder for age group chart -->
                    <canvas id="ageChart"></canvas>
                </div>

                <div class="chart">
                    <h3>Blood Group Distribution</h3>
                    <!-- Placeholder for blood group distribution chart -->
                    <canvas id="bloodChart"></canvas>
                </div>
            </section>
            <section class="donors_list">
                <h3>Recent Donors</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Blood Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Check if 'recent_donors' is already set in the session
                        if (!isset($_SESSION['recent_donors'])) {
                            $_SESSION['recent_donors'] = [];
                            // Query to fetch the last 3 entries from the donors table
                            $query = "SELECT name, governorate, Blood_Type FROM donerstable ORDER BY id DESC LIMIT 3";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                // Populate recent donors into the session array
                                while ($row = $result->fetch_assoc()) {
                                    $_SESSION['recent_donors'][] = [
                                        'name' => $row['name'],
                                        'location' => $row['governorate'],
                                        'Blood_Type' => $row['Blood_Type'],
                                    ];
                                }
                            }
                        } 
                        // Display recent donors or a fallback message if none exist
                        if(count($_SESSION['recent_donors']) > 0){
                            foreach ($_SESSION['recent_donors'] as $donor) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($donor['name']) . "</td>
                                    <td>" . htmlspecialchars($donor['location']) . "</td>
                                    <td>" . htmlspecialchars($donor['Blood_Type']) . "</td>
                                </tr>";
                            }
                        }else{
                            echo "<tr><td colspan='3'>No recent donors available.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <?php
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
        $conn->close(); // Close the database connection.
    ?>
    <script>
    const ctxAge = document.getElementById('ageChart').getContext('2d');
    const ageChart = new Chart(ctxAge, {
        type: 'pie',
        data: {
            labels: ['18-25', '26-35', '36-45', '46-55', '56+'],
            datasets: [{
                label: 'number of Donors',
                data: <?php echo json_encode(array_values($_SESSION['Donors_by_Age_Group'] ?? [])); ?>,
                backgroundColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(201, 203, 207, 0.5)',
                ],
                borderColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(201, 203, 207, 0.5)',
                ],
                borderWidth: 1
            }]
        }
    });

    const ctxBlood = document.getElementById('bloodChart').getContext('2d');
    const bloodChart = new Chart(ctxBlood, {
        type: 'pie',
        data: {
            labels: ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'],
            datasets: [{
                label: 'Blood Group Distribution',
                data: <?php echo json_encode(array_values($_SESSION['Blood_Group_Distribution'] ?? [])); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(201, 203, 207, 0.5)',
                    'rgba(29, 83, 94, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(201, 203, 207, 1)',
                    'rgba(29, 83, 94, 0.5)'
                ],
                borderWidth: 1
            }]
        }
    });
    </script>
</body>

</html>