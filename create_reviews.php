<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "holomia_vr", 3306);

if ($mysqli->connect_errno) {
    file_put_contents('db_check.txt', "Failed to connect to MySQL: " . $mysqli->connect_error);
    exit();
}

$query = "CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `order_item_id` bigint(20) unsigned DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_ticket_id_foreign` (`ticket_id`),
  KEY `reviews_order_item_id_foreign` (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($query) === TRUE) {
    file_put_contents('db_check.txt', "Table created successfully.");
} else {
    file_put_contents('db_check.txt', "Error creating table: " . $mysqli->error);
}

$mysqli->close();
