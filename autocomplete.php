<?php
$keyword = strtolower($_GET['keyword'] ?? '');
$suggestions = [];

if (($handle = fopen("final_dataset.csv", "r")) !== FALSE) {
    $header = fgetcsv($handle); // Ambil header
    while (($row = fgetcsv($handle)) !== FALSE) {
        $data = array_combine($header, $row);
        $judul = $data['judul'] ?? '';
        if ($judul && strpos(strtolower($judul), $keyword) !== false) {
            $suggestions[] = $judul;
        }
    }
    fclose($handle);
}

// Buang duplikat dan batasi ke 10 saran teratas
$suggestions = array_slice(array_unique($suggestions), 0, 10);

// Tampilkan dalam bentuk <li>
foreach ($suggestions as $s) {
    echo "<li class='px-4 py-2 cursor-pointer hover:bg-gray-200'>" . htmlspecialchars($s) . "</li>";
}
?>
