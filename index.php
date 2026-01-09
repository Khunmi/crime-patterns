<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Analyses</title>

    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.0/leaflet.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="source/jquery-ui.min.css">
    <link rel="stylesheet" href="plugins/sidebar/leaflet-sidebar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://ppete2.github.io/Leaflet.PolylineMeasure/Leaflet.PolylineMeasure.css" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css"/>
    <link rel="stylesheet" href="plugins/minimap/Control.MiniMap.css">
    <link rel="stylesheet" href="plugins/zoombar/L.Control.ZoomBar.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.min.css">



    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.0/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.0/leaflet-src.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="source/jquery-ui.min.js"></script>
    <script src="plugins/sidebar/leaflet-sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>
    <script src="https://ppete2.github.io/Leaflet.PolylineMeasure/Leaflet.PolylineMeasure.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>
    <script src="plugins/minimap/Control.MiniMap.js"></script>
    <script src="plugins/zoombar/L.Control.ZoomBar.js"></script>
    <script src="plugins/ajax/leaflet.ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.17/dist/sweetalert2.all.min.js"></script>

</head>

<body>
    <div class="page-shell">
        <header class="hero-band">
            <div class="container-fluid">
                <div class="hero-title">
                    <p class="eyebrow">Citywide Safety Analytics</p>
                    <h1>Crime Patterns in <span id="cityLabel">Edmonton</span></h1>
                    <p class="subtitle">Track emerging hotspots, weekly trends, and neighborhood-level shifts at a glance.</p>
                </div>
                <div class="stat-grid">
                    <div class="stat-card">
                        <p class="stat-label">Total Incidents</p>
                        <h2 class="stat-value" id="statTotal">--</h2>
                        <p class="stat-meta" id="statRange">Last 30 days</p>
                    </div>
                    <div class="stat-card highlight">
                        <p class="stat-label">Top Category</p>
                        <h2 class="stat-value" id="statTopCategory">--</h2>
                        <p class="stat-meta" id="statTopCategoryMeta">--</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Distinct Intersections</p>
                        <h2 class="stat-value" id="statDistinctIntersections">--</h2>
                        <p class="stat-meta">Reported in selection</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Active Hotspots</p>
                        <h2 class="stat-value" id="statActiveHotspots">--</h2>
                        <p class="stat-meta" id="statHotspotMeta">5+ incidents per intersection</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="map-stage">
            <div class="map-wrapper">
                <div id="mapdiv"></div>
                <div id="sidebar" class="sidebar collapsed">
                    <div class="sidebar-tabs">
                        <ul role="tablist">
                            <li class="active"><a href="#filters" role="tab"><i class="fa fa-filter"></i></a></li>
                        </ul>
                        <ul role="tablist">
                            <li><a href="#about" role="tab"><i class="fa fa-info-circle"></i></a></li>
                        </ul>
                    </div>
                    <div class="sidebar-content">
                        <div class="sidebar-pane active" id="filters">
                            <h1 class="sidebar-header">
                                Filter the Map
                                <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                            </h1>
                            <form class="filter-form">
                                <div class="form-group">
                                    <label for="citySelect">City</label>
                                    <select id="citySelect" class="form-control">
                                        <option value="" disabled selected>Loading cities...</option>
                                    </select>
                                </div>
                        <div class="form-group">
                            <label for="crimeType">Occurrence Category</label>
                            <select id="crimeType" class="form-control">
                                <option value="" disabled selected>Loading categories...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dateRange">Date Range</label>
                            <div class="date-range">
                                <input id="startDate" type="date" class="form-control" placeholder="Start date">
                                <input id="endDate" type="date" class="form-control" placeholder="End date">
                            </div>
                        </div>
                        <button type="button" id="filter_crimes" class="btn btn-primary btn-block">Apply Filters</button>
                    </form>
                </div>
                        <div class="sidebar-pane" id="about">
                            <h1 class="sidebar-header">
                                About
                                <span class="sidebar-close"><i class="fa fa-caret-left"></i></span>
                            </h1>
                            <p class="sidebar-text">Crime analytics map powered by PostGIS and Leaflet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <section class="insights-band">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-7">
                        <div class="insight-card">
                            <div class="insight-header">
                                <h3>Monthly Trend</h3>
                                <span class="insight-meta">Crime incidents per month</span>
                            </div>
                            <ul class="timeseries-list" id="timeseriesList">
                                <li class="timeseries-item">Loading trend data...</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="insight-card">
                            <div class="insight-header">
                                <h3>Top Occurrence Groups</h3>
                                <span class="insight-meta">Highest counts in selection</span>
                            </div>
                            <ul class="insight-list" id="topGroupsList">
                                <li><span>Loading...</span><strong>--</strong></li>
                            </ul>
                        </div>
                        <div class="insight-card">
                            <div class="insight-header">
                                <h3>Top Occurrence Types</h3>
                                <span class="insight-meta">Most frequent types</span>
                            </div>
                            <ul class="insight-list" id="topTypesList">
                                <li><span>Loading...</span><strong>--</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        var map;
        var baseLayers;
        var overlays;
        var crimeLayers = {};
        var heatLayers = {};
        var heatLegend;
        var heatGridSize = 0.004;
        
        $.ajaxSetup({
            headers: {
                'X-WEBMAP-TOKEN': 'Y389470d3872dfeac228dee57f0c5643b002c227ef2b6d9f991bfa2a7de23552b'
            }
        });

        /* Map setup and base layers. */
        map = L.map('mapdiv', {
            center: [53.5461, -113.4938],
            zoom: 11,
            attributionControl: false,
            zoomControl: false

        });

        var GoogleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 23,
            subdomains:['mt0','mt1','mt2','mt3']
        });

        var openstreetmap = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png?', {maxZoom:23});

        var CartoDB_Positron = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {maxZoom: 23});

        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {maxZoom: 23});

        var OpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {maxZoom: 23,});

        openstreetmap.addTo(map);

        overlays = {};
        var sidebar = L.control.sidebar('sidebar', {
            position: 'left'
        });
        map.addControl(sidebar);

        map.on('click', function() {
            sidebar.close();
        });

        $('.map-wrapper').on('click', function(event) {
            if ($(event.target).closest('#sidebar, .leaflet-control').length) {
                return;
            }
            sidebar.close();
        });

        /* Filter helpers and UI defaults. */
        function formatDate(value) {
            var year = value.getFullYear();
            var month = String(value.getMonth() + 1).padStart(2, '0');
            var day = String(value.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function setDateRangeFromLatest(latestDate) {
            var endDate = latestDate ? new Date(latestDate) : new Date();
            if (isNaN(endDate.getTime())) {
                endDate = new Date();
            }
            var startDate = new Date(endDate);
            startDate.setMonth(startDate.getMonth() - 5);
            $('#startDate').val(formatDate(startDate));
            $('#endDate').val(formatDate(endDate));
        }

        function loadLatestDate(city, callback) {
            var cityValue = city || 'all';
            $.ajax({
                url: 'api_calls/latest_date.php',
                type: 'POST',
                dataType: 'json',
                data: { city_name: cityValue },
                success: function(response) {
                    setDateRangeFromLatest(response.latest_date);
                    if (callback) {
                        callback();
                    }
                },
                error: function() {
                    setDateRangeFromLatest('');
                    if (callback) {
                        callback();
                    }
                }
            });
        }

        function getDateRange() {
            return {
                start: $('#startDate').val().trim(),
                end: $('#endDate').val().trim()
            };
        }

        function updateSummary(summary, rangeLabel) {
            $('#statTotal').text(summary.total.toLocaleString());
            $('#statTopCategory').text(summary.top_category || 'N/A');
            $('#statTopCategoryMeta').text(summary.top_category_count + ' incidents');
            $('#statDistinctIntersections').text(summary.distinct_intersections.toLocaleString());
            $('#statActiveHotspots').text(summary.active_hotspots.toLocaleString());
            $('#statRange').text(rangeLabel);
            $('#statHotspotMeta').text(summary.hotspot_threshold + '+ incidents per intersection');
        }

        function updateList(listId, items) {
            var $list = $(listId);
            $list.empty();
            if (!items.length) {
                $list.append('<li><span>No data</span><strong>--</strong></li>');
                return;
            }
            items.forEach(function(item) {
                $list.append('<li><span>' + item.label + '</span><strong>' + item.count.toLocaleString() + '</strong></li>');
            });
        }

        function updateTimeseries(timeseries) {
            var $list = $('#timeseriesList');
            $list.empty();
            if (!timeseries.length) {
                $list.append('<li class="timeseries-item">No trend data available.</li>');
                return;
            }
            var maxValue = Math.max.apply(null, timeseries.map(function(item) { return item.count; }));
            timeseries.forEach(function(item) {
                var percent = maxValue ? Math.round((item.count / maxValue) * 100) : 0;
                var label = item.bucket ? item.bucket.substring(0, 7) : 'Unknown';
                $list.append(
                    '<li class="timeseries-item">' +
                    '<span>' + label + '</span>' +
                    '<div class="timeseries-bar"><div style="width:' + percent + '%"></div></div>' +
                    '<strong>' + item.count.toLocaleString() + '</strong>' +
                    '</li>'
                );
            });
        }

        function buildHeatLegend() {
            heatLegend = L.control({position: 'topright'});
            heatLegend.onAdd = function() {
                var div = L.DomUtil.create('div', 'heat-legend');
                div.innerHTML =
                    '<div class="heat-legend-title">Incident Density</div>' +
                    '<div class="heat-legend-bar"></div>' +
                    '<div class="heat-legend-values">' +
                    '<div class="heat-legend-value"><span class="heat-legend-label">Low</span><span id="heatLegendLow">0</span></div>' +
                    '<div class="heat-legend-value"><span class="heat-legend-label">Mid</span><span id="heatLegendMid">0</span></div>' +
                    '<div class="heat-legend-value"><span class="heat-legend-label">High</span><span id="heatLegendHigh">0</span></div>' +
                    '</div>' +
                    '<div class="heat-legend-caption" id="heatLegendCaption">Counts per grid cell</div>';
                L.DomEvent.disableClickPropagation(div);
                L.DomEvent.disableScrollPropagation(div);
                return div;
            };
            heatLegend.addTo(map);
        }

        /* Heatmap legend updates based on aggregation counts. */
        function updateHeatLegend(counts) {
            if (!heatLegend || !counts) {
                return;
            }
            if (!counts.length) {
                $('#heatLegendLow').text('0');
                $('#heatLegendMid').text('0');
                $('#heatLegendHigh').text('0');
                $('#heatLegendCaption').text('No data for selection');
                return;
            }

            var sorted = counts.slice().sort(function(a, b) { return a - b; });
            var low = sorted[Math.floor((sorted.length - 1) * 0.1)];
            var mid = sorted[Math.floor((sorted.length - 1) * 0.5)];
            var high = sorted[Math.floor((sorted.length - 1) * 0.9)];

            $('#heatLegendLow').text(low.toLocaleString());
            $('#heatLegendMid').text(mid.toLocaleString());
            $('#heatLegendHigh').text(high.toLocaleString());

            var approxKm = (heatGridSize * 111).toFixed(2);
            $('#heatLegendCaption').text('Counts per ~' + approxKm + ' km grid');
        }

        function updateHeaderCity(city) {
            var label = city && city !== 'all' ? city : 'All Cities';
            $('#cityLabel').text(label);
        }

        /* Filter data loaders (cities, categories, date defaults). */
        function populateCities(cities) {
            var $select = $('#citySelect');
            $select.empty();

            if (!cities.length) {
                $select.append('<option value="all">All Cities</option>');
                updateHeaderCity('All Cities');
                return 'all';
            }

            cities.forEach(function(city) {
                if (city) {
                    $select.append('<option value="' + city + '">' + city + '</option>');
                }
            });

            var defaultCity = 'Edmonton';
            var selected = cities.indexOf(defaultCity) !== -1 ? defaultCity : cities[0];
            $select.val(selected);
            updateHeaderCity(selected);
            return selected;
        }

        function populateCategories(categories) {
            var $select = $('#crimeType');
            var previous = $select.val() || 'all';
            $select.empty();
            $select.append('<option value="all">All Categories</option>');

            categories.forEach(function(category) {
                if (category) {
                    $select.append('<option value="' + category + '">' + category + '</option>');
                }
            });

            if (previous !== 'all' && categories.indexOf(previous) !== -1) {
                $select.val(previous);
            } else {
                $select.val('all');
            }
        }

        function loadCategories(city, callback) {
            var cityValue = city || 'all';
            $.ajax({
                url: 'api_calls/categories.php',
                type: 'POST',
                dataType: 'json',
                data: { city_name: cityValue },
                success: function(response) {
                    var categories = Array.isArray(response.categories) ? response.categories : [];
                    populateCategories(categories);
                    if (callback) {
                        callback();
                    }
                },
                error: function() {
                    populateCategories([]);
                    if (callback) {
                        callback();
                    }
                }
            });
        }

        function loadCities() {
            $.ajax({
                url: 'api_calls/cities.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    var cities = Array.isArray(response.cities) ? response.cities : [];
                    var selected = populateCities(cities);
                    loadLatestDate(selected, function() {
                        loadCategories(selected, applyFilters);
                    });
                },
                error: function() {
                    populateCities(['Edmonton']);
                    loadLatestDate('Edmonton', function() {
                        loadCategories('Edmonton', applyFilters);
                    });
                }
            });
        }

        function styleCrimes(feature, latlng) {
            return L.circleMarker(latlng, {
                radius: 5,
                fillColor: '#f05a28',
                color: '#ffffff',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            });
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildTooltip(props) {
            if (!props) {
                return 'No details';
            }
            var rows = [];
            function addRow(label, value) {
                if (value !== null && value !== undefined && value !== '') {
                    rows.push('<div><span class="crime-tooltip-label">' + label + ':</span> ' + escapeHtml(value) + '</div>');
                }
            }

            addRow('Category', props.occurrence_category);
            addRow('Group', props.occurrence_group);
            addRow('Type', props.occurrence_type_group);
            addRow('Date', props.date_reported || props.reported_date);
            addRow('Intersection', props.intersection);
            addRow('City', props.city_name);

            if (!rows.length) {
                return 'No details';
            }
            return '<div class="crime-tooltip">' + rows.join('') + '</div>';
        }

        /* Point layer loader (crime features). */
        function loadCrimes() {
            var range = getDateRange();
            var cityValue = $('#citySelect').val() || 'all';
            var cityLabel = cityValue && cityValue !== 'all' ? cityValue : 'All Cities';
            var layerKey = cityValue && cityValue !== 'all' ? cityValue.toLowerCase() : 'all';
            var params = {
                city_name: cityValue,
                occurrence_category: $('#crimeType').val(),
                start_date: range.start,
                end_date: range.end
            };

            $.ajax({
                url: 'api_calls/load_crimes.php',
                type: 'POST',
                data: params,
                success: function(response) {
                    if (response.trim().substr(0, 5) === 'ERROR') {
                        console.log(response);
                        return;
                    }

                    var jsnCrimes = JSON.parse(response);
                    if (crimeLayers[layerKey]) {
                        map.removeLayer(crimeLayers[layerKey]);
                        controlLayers.removeLayer(crimeLayers[layerKey]);
                    }

                    var cityLayer = L.geoJSON(jsnCrimes, {
                        pointToLayer: styleCrimes,
                        onEachFeature: function(feature, layer) {
                            layer.bindTooltip(buildTooltip(feature.properties), {
                                sticky: true,
                                direction: 'top',
                                opacity: 0.9
                            });
                        }
                    }).addTo(map);

                    crimeLayers[layerKey] = cityLayer;
                    controlLayers.addOverlay(cityLayer, 'Reported Crimes - ' + cityLabel);

                    if (cityLayer.getBounds().isValid()) {
                        map.fitBounds(cityLayer.getBounds());
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error loading crimes data: " + error);
                }
            });
        }

        /* Heatmap loader (server-side aggregation). */
        function loadHeatmap() {
            var range = getDateRange();
            var cityValue = $('#citySelect').val() || 'all';
            var cityLabel = cityValue && cityValue !== 'all' ? cityValue : 'All Cities';
            var layerKey = cityValue && cityValue !== 'all' ? cityValue.toLowerCase() : 'all';
            var params = {
                city_name: cityValue,
                occurrence_category: $('#crimeType').val(),
                grid_size: heatGridSize,
                start_date: range.start,
                end_date: range.end
            };

            $.ajax({
                url: 'api_calls/heat_crimes.php',
                type: 'POST',
                dataType: 'json',
                data: params,
                success: function(response) {
                    if (!response || !Array.isArray(response.points)) {
                        return;
                    }

                    if (heatLayers[layerKey]) {
                        map.removeLayer(heatLayers[layerKey]);
                        controlLayers.removeLayer(heatLayers[layerKey]);
                    }

                    if (!response.points.length) {
                        updateHeatLegend([]);
                        return;
                    }

                    var counts = response.points.map(function(point) {
                        return point.count;
                    });
                    var maxCount = Math.max.apply(null, counts);
                    var heatPoints = response.points.map(function(point) {
                        return [point.lat, point.lng, Math.sqrt(point.count)];
                    });
                    var maxWeight = Math.sqrt(maxCount || 1);

                    var heatLayer = L.heatLayer(heatPoints, {
                        radius: 20,
                        blur: 25,
                        minOpacity: 0.38,
                        maxZoom: 17,
                        max: maxWeight,
                        gradient: {
                            0.0: '#1f2a44',
                            0.3: '#2b6cb0',
                            0.55: '#38bdf8',
                            0.75: '#facc15',
                            1.0: '#ef4444'
                        }
                    }).addTo(map);

                    heatLayers[layerKey] = heatLayer;
                    controlLayers.addOverlay(heatLayer, 'Crime Heatmap - ' + cityLabel);
                    updateHeatLegend(counts);
                    var bounds = L.latLngBounds(heatPoints.map(function(point) {
                        return [point[0], point[1]];
                    }));
                    if (bounds.isValid()) {
                        map.fitBounds(bounds);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error loading heatmap data: " + error);
                }
            });
        }

        /* Stats loader for cards, lists, and time series. */
        function fetchStats() {
            var range = getDateRange();
            var cityValue = $('#citySelect').val() || 'all';
            var params = {
                city_name: cityValue,
                occurrence_category: $('#crimeType').val(),
                start_date: range.start,
                end_date: range.end
            };

            $.ajax({
                url: 'api_calls/stats_crimes.php',
                type: 'POST',
                dataType: 'json',
                data: params
            })
                .done(function(response) {
                    var rangeLabel = range.start && range.end ? (range.start + ' - ' + range.end) : 'All available dates';
                    updateSummary(response.summary, rangeLabel);
                    updateList('#topGroupsList', response.top_groups || []);
                    updateList('#topTypesList', response.top_types || []);
                    updateTimeseries(response.timeseries || []);
                })
                .fail(function() {
                    updateSummary({
                        total: 0,
                        top_category: 'N/A',
                        top_category_count: 0,
                        distinct_intersections: 0,
                        active_hotspots: 0,
                        hotspot_threshold: 5
                    }, 'Unavailable');
                    updateList('#topGroupsList', []);
                    updateList('#topTypesList', []);
                    updateTimeseries([]);
                });
        }

        function applyFilters() {
            fetchStats();
            loadCrimes();
            loadHeatmap();
        }

        $(function() {
            loadCities();

            $('#filter_crimes').on('click', applyFilters);
            $('#citySelect').on('change', function() {
                updateHeaderCity($(this).val());
                loadLatestDate($(this).val(), function() {
                    loadCategories($(this).val(), applyFilters);
                }.bind(this));
            });
        });

        

        // ZOOM BAR
        var zoomBar = new L.Control.ZoomBar({ position: 'topright' }).addTo(map);

        // LAYER CONTROL
        baseLayers = {
            "Google Streets": GoogleStreets,
            "OSM": openstreetmap,
            "CartoDB Positron": CartoDB_Positron,
            "ESRI World Imagery": Esri_WorldImagery,
            "Open Topo Map": OpenTopoMap,
        };
        var controlLayers = L.control.layers(baseLayers, overlays).addTo(map);
        
        // MEASURE TOOL
        L.control.polylineMeasure({position:'topright'}).addTo(map);

        buildHeatLegend();

        // Geoman controls are intentionally disabled for read-only analytics.

        // MINIMAP
        // var miniMap = new L.Control.MiniMap(GoogleStreets,{position:'bottomright', height: 120, width: 120}).addTo(map);

        // SCALE
        L.control.scale({position:'bottomright', maxwidth: '300', imperial: false}).addTo(map);
    </script>
</body>
</html>
