echo '<?php
try {
    $pdo = new PDO("sqlite::memory:");
    echo "SQLite connection successful!\n";
    
    $drivers = PDO::getAvailableDrivers();
    echo "Available PDO drivers: " . implode(", ", $drivers) . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}