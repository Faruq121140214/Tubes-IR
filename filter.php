<?php
$keyword = strtolower($_GET['keyword'] ?? '');
$year = $_GET['year'] ?? '';

$results = [];
if (($handle = fopen("final_dataset.csv", "r")) !== FALSE) {
    $header = fgetcsv($handle); // Ambil header
    while (($row = fgetcsv($handle)) !== FALSE) {
        $data = array_combine($header, $row);
        $judul = strtolower($data['judul'] ?? '');
        $penulis = strtolower($data['penulis'] ?? '');
        $tahun = $data['tahun'] ?? '';

        if (
            ($keyword === '' || strpos($judul, $keyword) !== false || strpos($penulis, $keyword) !== false) &&
            ($year === '' || $tahun === $year)
        ) {
            $results[] = $data;
        }
    }
    fclose($handle);
}

// Tampilkan hasil
if (count($results) > 0) {
    foreach ($results as $row) {
        echo "<div class='border-b border-gray-300 py-2'>";
        echo "<p class='font-semibold text-blue-700'>" . htmlspecialchars($row['judul']) . "</p>";
        echo "<p class='text-sm text-gray-600'>" . htmlspecialchars($row['penulis']) . " - " . htmlspecialchars($row['tahun']) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p class='text-gray-500'>Tidak ada hasil yang cocok.</p>";
}
?>
