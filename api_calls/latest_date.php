<?php
    /**
     * Return the latest available report date for a city.
     * Inputs (POST): city_name.
     * Output: JSON {latest_date} or JSON error payload.
     */
    require_once __DIR__ . '/init.php';

    header('Content-Type: application/json');

    $city = htmlspecialchars($_POST['city_name'] ?? '', ENT_QUOTES);
    $conditions = [];
    $params = [];

    if ($city !== '' && strtolower($city) !== 'all') {
        $conditions[] = 'LOWER(city_name) = LOWER(:city_name)';
        $params[':city_name'] = $city;
    }

    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT MAX(date_reported) AS latest_date FROM crimes_master $where";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $latest = $stmt->fetchColumn();

        echo json_encode([
            'latest_date' => $latest
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'latest_date' => null,
            'error' => $e->getMessage()
        ]);
    }
?>
