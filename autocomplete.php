<?php
$term = $_GET['keyword'] ?? '';
if (empty($term)) exit;

$url = 'http://localhost:5000/autocomplete?term=' . urlencode($term);
$response = file_get_contents($url);
$suggestions = json_decode($response, true);

if ($suggestions) {
  foreach ($suggestions as $word) {
    echo "<li class='px-4 py-2 hover:bg-blue-100 cursor-pointer'>" . htmlspecialchars($word) . "</li>";
  }
}
?>
