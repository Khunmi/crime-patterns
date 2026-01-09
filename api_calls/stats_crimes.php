<?php
    /**
     * Aggregate statistics for the dashboard cards, lists, and time series.
     * Inputs (POST): city_name, occurrence_category, occurrence_group, occurrence_type_group, start_date, end_date.
     * Output: JSON {summary, timeseries, top_groups, top_types} or JSON error payload.
     */
    require_once __DIR__ . '/init.php';

    header('Content-Type: application/json');

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

    try {
        $summary = [
            'total' => 0,
            'top_category' => 'N/A',
            'top_category_count' => 0,
            'distinct_intersections' => 0,
            'active_hotspots' => 0,
            'hotspot_threshold' => 5
        ];

        $stmt_total = $pdo->prepare("SELECT COUNT(*) AS total FROM crimes_master $where");
        $stmt_total->execute($params);
        $summary['total'] = (int) $stmt_total->fetchColumn();

        $stmt_top_category = $pdo->prepare("
            SELECT occurrence_category, COUNT(*) AS total
            FROM crimes_master
            $where
            GROUP BY occurrence_category
            ORDER BY total DESC NULLS LAST
            LIMIT 1
        ");
        $stmt_top_category->execute($params);
        $top_category = $stmt_top_category->fetch();
        if ($top_category) {
            $summary['top_category'] = $top_category['occurrence_category'] ?: 'N/A';
            $summary['top_category_count'] = (int) $top_category['total'];
        }

        $intersection_condition = "intersection IS NOT NULL AND intersection <> ''";
        $where_intersection = $where ? "$where AND $intersection_condition" : "WHERE $intersection_condition";

        $stmt_distinct = $pdo->prepare("
            SELECT COUNT(DISTINCT intersection) AS total
            FROM crimes_master
            $where_intersection
        ");
        $stmt_distinct->execute($params);
        $summary['distinct_intersections'] = (int) $stmt_distinct->fetchColumn();

        $hotspot_threshold = 5;
        $stmt_hotspots = $pdo->prepare("
            SELECT COUNT(*) AS total
            FROM (
                SELECT intersection, COUNT(*) AS cnt
                FROM crimes_master
                $where_intersection
                GROUP BY intersection
                HAVING COUNT(*) >= :hotspot_threshold
            ) AS hotspots
        ");
        $hotspot_params = $params;
        $hotspot_params[':hotspot_threshold'] = $hotspot_threshold;
        $stmt_hotspots->execute($hotspot_params);
        $summary['active_hotspots'] = (int) $stmt_hotspots->fetchColumn();

        $stmt_timeseries = $pdo->prepare("
            SELECT date_trunc('month', date_reported)::date AS bucket, COUNT(*) AS total
            FROM crimes_master
            $where
            GROUP BY bucket
            ORDER BY bucket
        ");
        $stmt_timeseries->execute($params);
        $timeseries = [];
        while ($row = $stmt_timeseries->fetch()) {
            $timeseries[] = [
                'bucket' => $row['bucket'],
                'count' => (int) $row['total']
            ];
        }

        $stmt_groups = $pdo->prepare("
            SELECT occurrence_group AS label, COUNT(*) AS total
            FROM crimes_master
            $where
            GROUP BY occurrence_group
            ORDER BY total DESC NULLS LAST
            LIMIT 5
        ");
        $stmt_groups->execute($params);
        $top_groups = [];
        while ($row = $stmt_groups->fetch()) {
            $top_groups[] = [
                'label' => $row['label'] ?: 'Unknown',
                'count' => (int) $row['total']
            ];
        }

        $stmt_types = $pdo->prepare("
            SELECT occurrence_type_group AS label, COUNT(*) AS total
            FROM crimes_master
            $where
            GROUP BY occurrence_type_group
            ORDER BY total DESC NULLS LAST
            LIMIT 5
        ");
        $stmt_types->execute($params);
        $top_types = [];
        while ($row = $stmt_types->fetch()) {
            $top_types[] = [
                'label' => $row['label'] ?: 'Unknown',
                'count' => (int) $row['total']
            ];
        }

        echo json_encode([
            'summary' => $summary,
            'timeseries' => $timeseries,
            'top_groups' => $top_groups,
            'top_types' => $top_types
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Database query failed.',
            'details' => $e->getMessage()
        ]);
    }
?>
