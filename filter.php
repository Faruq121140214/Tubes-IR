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
    echo "<div class='p-4 bg-gray-100 rounded-lg border mb-3'>";

    echo "<p class='font-semibold text-blue-700 mb-1'>" . htmlspecialchars($item['input']) . "</p>";

    // Decode output JSON (Final_Output)
    $output = json_decode($item['output'], true);

    if (is_array($output)) {
      echo "<p class='text-sm text-gray-700'>Judul: " . htmlspecialchars($output['title'] ?? '-') . "</p>";
      echo "<p class='text-sm text-gray-700'>Author: " . htmlspecialchars($output['author'] ?? '-') . "</p>";
      echo "<p class='text-sm text-gray-700'>Journal: " . htmlspecialchars($output['journalName'] ?? '-') . "</p>";
      echo "<p class='text-sm text-gray-700'>Tahun: " . htmlspecialchars($output['year'] ?? '-') . "</p>";
      echo "<p class='text-sm text-gray-700'>Volume: " . htmlspecialchars($output['volume'] ?? '-') . "</p>";
      echo "<p class='text-sm text-gray-700'>Pages: " . htmlspecialchars($output['page'] ?? '-') . "</p>";
    } else {
      echo "<p class='text-sm text-gray-500 italic'>Output tidak dikenali.</p>";
    }

    echo "</div>";
  }
} else {
  echo "<p class='text-gray-600'>Tidak ada hasil ditemukan.</p>";
}
?>
