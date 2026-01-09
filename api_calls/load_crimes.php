<?php
    /**
     * Load crime points as GeoJSON for the map.
     * Inputs (POST): city_name, occurrence_category, occurrence_group, occurrence_type_group, start_date, end_date.
     * Output: GeoJSON FeatureCollection or "ERROR ..." on failure.
     */
    require_once __DIR__ . '/init.php';

    $city = htmlspecialchars($_POST['city_name'] ?? '', ENT_QUOTES);
    $category = htmlspecialchars($_POST['occurrence_category'] ?? '', ENT_QUOTES);
    $group = htmlspecialchars($_POST['occurrence_group'] ?? '', ENT_QUOTES);
    $type_group = htmlspecialchars($_POST['occurrence_type_group'] ?? '', ENT_QUOTES);
    $start_date = htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES);
    $end_date = htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES);

    $conditions = [];
    $params = [];

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

    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT *, ST_AsGeoJSON(geom) AS geojson FROM crimes_master $where";

    try {
        $result = $pdo->prepare($sql);
        $result->execute($params);
        $features = [];

        foreach ($result as $row) {
            unset($row['geom']);
            $geometry = json_decode($row['geojson']);
            unset($row['geojson']);

            $feature = ["type" => "Feature", "geometry" => $geometry, "properties" => $row];
            array_push($features, $feature);
        }

        $featureCollection = ["type" => "FeatureCollection", "features" => $features];
        echo json_encode($featureCollection);
    } catch (PDOException $e) {
        echo "ERROR " . $e->getMessage();
    }
?>
