<?php
    /**
     * Bootstrap database access and write-token validation.
     * Inputs: config file or environment variables for DSN/user/pass/token.
     * Output: initialized PDO handle and helper function.
     */
    ob_start();
    session_start();

    try {

        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];

        $config_path = '/Applications/XAMPP/xamppfiles/htdocs/config/db.php';
        $config = [];
        if (file_exists($config_path)) {
            $config = require $config_path;
        }

        $dsn = $config['dsn'] ?? getenv('WEBMAP_DSN');
        $db_user = $config['user'] ?? getenv('WEBMAP_DB_USER');
        $db_pass = $config['pass'] ?? getenv('WEBMAP_DB_PASS');

        if (!$dsn || !$db_user || !$db_pass) {
            throw new PDOException('Missing database configuration.');
        }

        $pdo = new PDO($dsn, $db_user, $db_pass, $opt);

    } catch(PDOException $e) {
        echo "Error: ".$e->getMessage();
    }

    function require_write_token() {
        $config_path = 'home/kunmiade/config/db.php';
        $config = [];
        if (file_exists($config_path)) {
            $config = require $config_path;
        }

        $token = $config['token'] ?? getenv('WEBMAP_WRITE_TOKEN');
        if (!$token) {
            return;
        }

        $request_token = '';
        if (!empty($_SERVER['HTTP_X_WEBMAP_TOKEN'])) {
            $request_token = $_SERVER['HTTP_X_WEBMAP_TOKEN'];
        } elseif (isset($_POST['token'])) {
            $request_token = $_POST['token'];
        }

        if (!$request_token || !hash_equals($token, $request_token)) {
            http_response_code(403);
            exit('ERROR Unauthorized');
        }
    }
?>
