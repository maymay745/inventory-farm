<?php
// Assuming this is your database connection (make sure to include the correct connection settings)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_systems";

// Create a connection to the database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to fetch all reports, ordered by the report date in descending order
$query = "SELECT * FROM reports ORDER BY report_date DESC";

// Execute the query
$result = mysqli_query($conn, $query);

// Start HTML Output
echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Reports</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-gray-100 font-sans'>";

// Page Heading
echo "<div class='container mx-auto py-6'>";
echo "<h1 class='text-3xl font-semibold text-gray-800 text-center mb-6'>Report List</h1>";

// Check if any reports were returned
if (mysqli_num_rows($result) > 0) {
    // Start a grid layout for the reports
    echo "<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6'>";

    // Output the reports
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='bg-white shadow-lg rounded-lg p-6 hover:shadow-2xl transition-transform duration-300 transform hover:scale-105'>";
        
        // Report ID
        echo "<h3 class='text-xl font-semibold text-gray-800 mb-4'>Report ID: " . $row['report_id'] . "</h3>";

        // Report Details
        echo "<p class='text-gray-600 mb-2'><strong>Title:</strong> " . $row['title'] . "</p>";
        echo "<p class='text-gray-600 mb-2'><strong>Date:</strong> " . $row['report_date'] . "</p>";
        echo "<p class='text-gray-600 mb-2'><strong>Created By:</strong> " . $row['created_by'] . "</p>";
        echo "<p class='text-gray-600 mb-2'><strong>Description:</strong> " . $row['description'] . "</p>";

        // Check if there is a file and provide a link to download it
        if ($row['file_path']) {
            echo "<p class='text-gray-600 mb-2'><strong>File:</strong> <a href='" . $row['file_path'] . "' target='_blank' class='text-blue-500 hover:underline'>Download</a></p>";
        } else {
            echo "<p class='text-gray-600 mb-2'><strong>File:</strong> No file attached</p>";
        }

        // Status
        echo "<p class='text-gray-600 mb-2'><strong>Status:</strong> " . $row['status'] . "</p>";

        echo "</div>"; // End Report Card
    }
    echo "</div>"; // End Grid Layout
} else {
    // If no reports are found
    echo "<p class='text-center text-gray-500'>No reports found.</p>";
}

// Close the database connection
mysqli_close($conn);

// Close HTML Tags
echo "</div>"; // End Container
echo "</body>";
echo "</html>";
?>
