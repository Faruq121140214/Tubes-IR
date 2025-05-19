<?php
$keyword = $_GET['keyword'] ?? '';
$year = $_GET['year'] ?? '';

$params = http_build_query([
  'keyword' => $keyword,
  'year' => $year
]);

$url = 'http://localhost:5000/search?' . $params;
$response = file_get_contents($url);
$results = json_decode($response, true);

if ($results) {
  foreach ($results as $item) {
    echo "<div class='p-4 bg-gray-100 rounded-lg border'>";
    echo "<p class='font-semibold text-blue-700 mb-1'>" . htmlspecialchars($item['input']) . "</p>";
    echo "<p class='text-sm text-gray-700'>" . htmlspecialchars($item['output']) . "</p>";
    echo "</div>";
  }
} else {
  echo "<p class='text-gray-600'>Tidak ada hasil ditemukan.</p>";
}
?>
