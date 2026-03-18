<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "holomia_vr", 3306);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

echo "Connected successfully to " . $mysqli->host_info . "\n";
echo "Server info: " . $mysqli->server_info . "\n";

$result = $mysqli->query("SHOW TABLES");
echo "Tables in holomia_vr:\n";
while ($row = $result->fetch_row()) {
    echo "- " . $row[0] . "\n";
}

$mysqli->close();
