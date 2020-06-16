<?php
define('DATA_FILE', './places.csv');
define('EARTH_RADIUS', 6371000); // metres
require './Point.php';

header('Content-Type: application/json; charset=UTF-8');

/** some kind of controller **/
if (array_key_exists('getDictionary', $_GET)) {
    $dict = getSource();
    $values = array_column($dict, 'name');
    echo json_encode($values);
}

if (array_key_exists('startPoint', $_GET) && array_key_exists('distance', $_GET)) {
    $dict = getSource();
    $values = getResults($_GET['startPoint'], $_GET['distance'], $dict);
    echo json_encode($values);
}

/** some kind of model **/
function getSource(): Array
{
    $result = [];
    $fh = fopen(DATA_FILE, 'r');
    if($fh !== false) {
        while(($data = fgetcsv($fh, 1024, ',')) !== false) {
            $name = trim($data[0]);
            list($latitude, $longitude) = explode(',', $data[1], 2);
            $result[] = new Point($name, $latitude, $longitude);
        }
    }
    return $result;
}

function getResults(String $startPoint = '', Int $distance = 0, Array $data = []): Array
{
    $items = [];
    if (empty($startPoint) || empty($distance)) {
        return $items;
    }

    $selectedIndex = array_search($startPoint, array_column($data, 'name'));
    $currentPoint = $data[$selectedIndex];

    foreach ($data as $item) {
        if ($item === $currentPoint) {
            continue;
        }
        $result = calculateDistance($currentPoint, $item);
        $result = round(($result / 1000), 2);
        if ($result <= $distance) {
            $items[] = [
                'name' => $item->name,
                'distance' => $result
            ];
        }
    }

    usort($items, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    return $items;
}

function calculateDistance(Point $posA, Point $posB): Float
{
    $latFrom = deg2rad($posA->latitude);
    $lonFrom = deg2rad($posA->longitude);
    $latTo = deg2rad($posB->latitude);
    $lonTo = deg2rad($posB->longitude);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return ($angle * EARTH_RADIUS);
}
