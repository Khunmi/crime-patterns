<?php
    /**
     * List occurrence categories for a selected city.
     * Inputs (POST): city_name.
     * Output: JSON {categories:[...]} or JSON error payload.
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

    $sql = "
        SELECT DISTINCT occurrence_category
        FROM crimes_master
        $where
        ORDER BY occurrence_category ASC
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $categories = [];

        foreach ($stmt as $row) {
            if ($row['occurrence_category'] !== null && $row['occurrence_category'] !== '') {
                $categories[] = $row['occurrence_category'];
            }
        }

        echo json_encode(['categories' => $categories]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'categories' => [],
            'error' => $e->getMessage()
        ]);
    }
?>
