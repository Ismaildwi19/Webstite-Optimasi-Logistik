<?php
header('Content-Type: application/json');

// Fungsi untuk menghitung jarak menggunakan formula Haversine
function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // Radius bumi dalam kilometer

    // Konversi derajat ke radian
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Perbedaan antara koordinat
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    // Formula Haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Jarak dalam kilometer
    return $earthRadius * $c;
}

// Fungsi untuk menghitung efisiensi logistik
function calculateLogistics($distance, $capacity, $cost_per_km, $average_fuel) {

    if ($capacity <= 1000) {
        $total_cost = (100000 / $capacity) + $distance * $cost_per_km * ($capacity / 1000) + (($distance / $average_fuel)*6800);
        $marginal_cost = (-100000 / ($capacity*$capacity)) + $distance * $cost_per_km / 1000;
        return [
            'total_cost' => round($total_cost, 2), // Biaya total dalam 2 desimal
            'marginal_cost' => round($marginal_cost, 2), // Biaya Marginal dalam 2 desimal
            'total_distance' => round($distance, 2), // Jarak dalam 2 desimal
        ];
    } elseif ($capacity <= 5000) {
        $total_cost = (300000 / $capacity) + $distance * $cost_per_km * ($capacity / 5000) + (($distance / $average_fuel)*6800);
        $marginal_cost = (-300000 / ($capacity*$capacity)) + $distance * $cost_per_km / 5000;
        return [
            'total_cost' => round($total_cost, 2), // Biaya total dalam 2 desimal
            'marginal_cost' => round($marginal_cost, 2), // Biaya Marginal dalam 2 desimal
            'total_distance' => round($distance, 2), // Jarak total dalam 2 desimal
        ];
    } elseif ($capacity <= 20000) {
        $total_cost = (750000 / $capacity) + $distance * $cost_per_km * ($capacity / 20000) + (($distance / $average_fuel)*6800);
        $marginal_cost = (-750000 / ($capacity*$capacity)) + $distance * $cost_per_km / 20000;
        return [
            'total_cost' => round($total_cost, 2), // Biaya total dalam 2 desimal
            'marginal_cost' => round($marginal_cost, 2), // Biaya Marginal dalam 2 desimal
            'total_distance' => round($distance, 2), // Jarak total dalam 2 desimal
        ];
    } else {
        $total_cost = (2000000 / $capacity) + $distance * $cost_per_km * ($capacity / 40000) + (($distance / $average_fuel)*6800);
        $marginal_cost = (-2000000 / ($capacity*$capacity)) + $distance * $cost_per_km / 40000;
        return [
            'total_cost' => round($total_cost, 2), // Biaya total dalam 2 desimal
            'marginal_cost' => round($marginal_cost, 2), // Biaya Marginal dalam 2 desimal
            'total_distance' => round($distance, 2), // Jarak total dalam 2 desimal
        ];
    }
    

}

// function transportSuggestion($capacity) {
//     if ($capacity <= 1000) {
//         return 'Truk Pickup';
//     } elseif ($capacity <= 5000) {
//         return 'Truk Box';
//     } elseif ($capacity <= 20000) {
//         return 'Truk Tronton';
//     } else {
//         return 'Truk Trailer';
//     }
// }

// Ambil data JSON dari request
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $start = $data['start']; // [latitude, longitude]
    $destination = $data['destination']; // [latitude, longitude]
    $cost_per_km = $data['cost'];
    $capacity = $data['capacity'];
    $average_fuel = $data['average_fuel'];

    // Hitung jarak menggunakan Haversine
    $distance = haversineDistance($start[0], $start[1], $destination[0], $destination[1]);

    // Hitung logistik
    $logistics = calculateLogistics($distance, $capacity, $cost_per_km, $average_fuel);

    // //Saran Transportasi
    // $transport = transportSuggestion($capacity);

    // Simulasi rute (titik awal dan akhir)
    $route = [
        [$start[0], $start[1]],  // Titik awal
        [$destination[0], $destination[1]]  // Titik tujuan
    ];

    // Kirim respons dalam format JSON
    echo json_encode([
        // 'transport' => $transport,
        'logistics' => $logistics,
        'route' => $route
    ]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
}
?>
