<?php

include(__DIR__ . '/../../config/config.php');

class Migration
{
    private static $queries = [
        "USE `" . DB_NAME . "`;",

        "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `email` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `password` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE `school_boards` (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `type` enum('CSM','CSMB') COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `school_boards_name_unique` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE `students` (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
            `id_school_board` int(20) unsigned NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            KEY `students_id_school_board_foreign` (`id_school_board`),
            CONSTRAINT `students_id_school_board_foreign` FOREIGN KEY (`id_school_board`) REFERENCES `school_boards` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

        "CREATE TABLE `grades` (
            `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
            `id_student` int(20) unsigned NOT NULL,
            `value` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `grades_id_student_foreign` (`id_student`),
            CONSTRAINT `grades_id_student_foreign` FOREIGN KEY (`id_student`) REFERENCES `students` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
    ];

    public static function run()
    {
        try {
            $options = array(\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
            $uri = DB_TYPE . ':host=' . DB_HOST .';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new \PDO($uri, DB_USER, DB_PASS, $options);
            foreach (self::$queries as $query) {
                $s = $pdo->exec($query);
            }
            print("\e[0;30;42mMigration successfull!\e[0m\n" . PHP_EOL);
        } catch (\PDOException $ex) {
            print($ex->getMessage());
            print("\e[0;30;41m" . $ex->getMessage() . "\e[0m\n" . PHP_EOL);
        } finally {
            $pdo = null;
        }
    }
}

Migration::run();