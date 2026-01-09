# Crime Analytics Webmap

An online analytics webmap for exploring reported crime patterns by city. The app uses
Leaflet on the frontend and PHP + PostGIS on the backend, with server-side aggregation
for a performant heatmap view.

## Features
- Interactive webmap with multiple basemaps.
- Server-side heatmap aggregation for large datasets.
- Dynamic filters: city, date range, occurrence category.
- Summary stats, top groups/types, and time-series breakdowns.
- Point layer with tooltips for detailed inspection.

## Stack
- Frontend: Leaflet, Bootstrap, jQuery
- Backend: PHP (PDO)
- Database: PostgreSQL + PostGIS

## Project Structure
```
crime_project/
  index.php                 # Main frontend
  styles.css                # Global styles
  api_calls/
    init.php                # DB bootstrap + token helper
    load_crimes.php          # GeoJSON points
    stats_crimes.php         # Summary stats + time series
    heat_crimes.php          # Server-side heat aggregation
    cities.php               # City list
    categories.php           # Categories by city
    latest_date.php          # Latest date by city
  plugins/                   # Leaflet plugins
  source/                    # Local JS/CSS dependencies
```

## Database Requirements
PostGIS-enabled PostgreSQL with a `crimes_master` table containing:
- `crime_database_id` (integer)
- `reported_date` (varchar)
- `occurrence_category` (varchar)
- `occurrence_group` (varchar)
- `occurrence_type_group` (varchar)
- `intersection` (varchar)
- `reported_day` (integer)
- `reported_month` (integer)
- `reported_year` (varchar)
- `objectid` (integer)
- `date_reported` (date)
- `geom` (geometry, MultiPoint, SRID 4326)
- `province` (text)
- `city_name` (text)

Recommended indexes:
```sql
CREATE INDEX crimes_master_geom_gix ON crimes_master USING GIST (geom);
CREATE INDEX crimes_master_date_idx ON crimes_master (date_reported);
CREATE INDEX crimes_master_city_idx ON crimes_master (city_name);
```

## Configuration
Create a config file at:
```
/Applications/XAMPP/xamppfiles/htdocs/config/db.php
```

Example:
```php
<?php
return [
    'dsn' => 'pgsql:host=HOST;dbname=DB;port=PORT;sslmode=require',
    'user' => 'DB_USER',
    'pass' => 'DB_PASS',
    'token' => 'WRITE_TOKEN'
];
```

The app also supports environment variables:
- `WEBMAP_DSN`
- `WEBMAP_DB_USER`
- `WEBMAP_DB_PASS`
- `WEBMAP_WRITE_TOKEN`

## Running Locally (XAMPP)
1. Start Apache (and PostgreSQL if local).
2. Place this project in:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/crime_project
   ```
3. Open:
   ```
   http://localhost/crime_project/
   ```

## API Endpoints
All endpoints accept `POST` parameters.

- `api_calls/load_crimes.php`
  - Returns GeoJSON FeatureCollection.
  - Inputs: `city_name`, `occurrence_category`, `occurrence_group`,
    `occurrence_type_group`, `start_date`, `end_date`.

- `api_calls/stats_crimes.php`
  - Returns summary stats and time series.
  - Inputs: same as above.

- `api_calls/heat_crimes.php`
  - Returns aggregated heatmap points.
  - Inputs: same as above plus `grid_size` (optional).

- `api_calls/cities.php`
  - Returns available cities.

- `api_calls/categories.php`
  - Returns categories for a selected city.
  - Inputs: `city_name`.

- `api_calls/latest_date.php`
  - Returns latest report date by city.
  - Inputs: `city_name`.

## Notes
- Default date range is the last 5 months from the latest available date per city.
- Heatmap aggregation uses grid snapping (configurable via `grid_size`).
- Point layer tooltips display key attributes per incident.

## Roadmap Ideas
- Add occurrence group/type filters.
- Add cached aggregation for high-traffic views.
- Replace the timeseries list with a charting library (ECharts/Chart.js).
- Optional vector tile or hexbin heatmap for smoother zoom behavior.

## License
Add a license that matches your intended use.
