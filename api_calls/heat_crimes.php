<?php
    /**
     * Aggregate crimes into grid-based heatmap points.
     * Inputs (POST): city_name, occurrence_category, occurrence_group, occurrence_type_group, start_date, end_date, grid_size.
     * Output: JSON {grid_size, points:[{lat,lng,count}]} or "ERROR ..." on failure.
     */
    require_once __DIR__ . '/init.php';

    $city = htmlspecialchars($_POST['city_name'] ?? '', ENT_QUOTES);
    $category = htmlspecialchars($_POST['occurrence_category'] ?? '', ENT_QUOTES);
    $group = htmlspecialchars($_POST['occurrence_group'] ?? '', ENT_QUOTES);
    $type_group = htmlspecialchars($_POST['occurrence_type_group'] ?? '', ENT_QUOTES);
    $start_date = htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES);
    $end_date = htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES);

    $grid_size = isset($_POST['grid_size']) ? floatval($_POST['grid_size']) : 0.01;
    if ($grid_size <= 0) {
        $grid_size = 0.01;
    }

    $conditions = [];
    $params = [
        ':grid_size' => $grid_size
    ];

    if ($city !== '' && strtolower($city) !== 'all') {
        $conditions[] = 'LOWER(city_name) = LOWER(:city_name)';
        $params[':city_name'] = $city;
    }

    if ($category !== '' && strtolower($category) !== 'all') {
        $conditions[] = 'occurrence_category = :occurrence_category';
        $params[':occurrence_category'] = $category;
    }

    if ($group !== '' && strtolower($group) !== 'all') {
        $conditions[] = 'occurrence_group = :occurrence_group';
        $params[':occurrence_group'] = $group;
    }

    if ($type_group !== '' && strtolower($type_group) !== 'all') {
        $conditions[] = 'occurrence_type_group = :occurrence_type_group';
        $params[':occurrence_type_group'] = $type_group;
    }

    if ($start_date !== '' && $end_date !== '') {
        $conditions[] = 'date_reported BETWEEN :start_date AND :end_date';
        $params[':start_date'] = $start_date;
        $params[':end_date'] = $end_date;
    } elseif ($start_date !== '') {
        $conditions[] = 'date_reported >= :start_date';
        $params[':start_date'] = $start_date;
    } elseif ($end_date !== '') {
        $conditions[] = 'date_reported <= :end_date';
        $params[':end_date'] = $end_date;
    }

    $conditions[] = 'geom IS NOT NULL';

    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "
        WITH filtered AS (
            SELECT (ST_Dump(geom)).geom AS geom
            FROM crimes_master
            $where
        ),
        snapped AS (
            SELECT ST_SnapToGrid(geom, :grid_size) AS geom
            FROM filtered
        )
        SELECT ST_Y(geom) AS lat, ST_X(geom) AS lng, COUNT(*) AS total
        FROM snapped
        GROUP BY geom
        ORDER BY total DESC
    ";

    try {
        $result = $pdo->prepare($sql);
        $result->execute($params);
        $points = [];

        foreach ($result as $row) {
            $points[] = [
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lng'],
                'count' => (int) $row['total']
            ];
        }

        echo json_encode([
            'grid_size' => $grid_size,
            'points' => $points
        ]);
    } catch (PDOException $e) {
        echo "ERROR " . $e->getMessage();
    }
?>
