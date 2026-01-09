<?php
    /**
     * List available cities in the dataset.
     * Inputs: none.
     * Output: JSON {cities:[...]} or "ERROR ..." on failure.
     */
    require_once __DIR__ . '/init.php';

    $sql = "SELECT DISTINCT city_name FROM crimes_master WHERE city_name IS NOT NULL AND city_name <> '' ORDER BY city_name ASC";

    try {
        $result = $pdo->query($sql);
        $cities = [];

        foreach ($result as $row) {
            $cities[] = $row['city_name'];
        }

        echo json_encode(['cities' => $cities]);
    } catch (PDOException $e) {
        echo "ERROR " . $e->getMessage();
    }
?>
