<?php
// Check if setup has already been completed
if (file_exists('setup_completed.flag')) {
    echo "Setup has already been completed. The SQL setup won't run again.";
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // Adjust according to your DB credentials
    define('DB_PASS', '');     // Adjust according to your DB credentials

    // Create Connection
    $link = new mysqli(DB_HOST, DB_USER, DB_PASS);

    // Check Connection
    if ($link->connect_error) {
        die('Connection Failed: ' . $link->connect_error);
    }

    // Create the 'restaurantdb' database if it doesn't exist
    $sqlCreateDB = "CREATE DATABASE IF NOT EXISTS restaurantdb";
    if ($link->query($sqlCreateDB) === TRUE) {
        echo "Database 'restaurantdb' created successfully.<br>";
    } else {
        echo "Error creating database: " . $link->error . "<br>";
    }

    // Switch to using the 'restaurantdb' database
    $link->select_db('restaurantdb');

    // Execute SQL statements from "restaurantdb.txt"
    function executeSQLFromFile($filename, $link) {
        $sql = file_get_contents($filename);
        
        // Ensure multi_query is used to handle multiple statements in the file
        if ($link->multi_query($sql)) {
            do {
                // Store result to skip through multiple results, if present
                if ($result = $link->store_result()) {
                    $result->free();
                }
            } while ($link->more_results() && $link->next_result());
            echo "SQL statements executed successfully.<br>";
            
            // Set the flag to indicate setup is complete
            file_put_contents('setup_completed.flag', 'Setup completed successfully.');
        } else {
            echo "Error executing SQL statements: " . $link->error;
        }
    }

    // Execute SQL statements from "restaurantdb.txt"
    executeSQLFromFile('restaurantdb.txt', $link);

    // Close the database connection
    $link->close();
}
?>

<a href="customerSide/home/home.php">Home</a>
