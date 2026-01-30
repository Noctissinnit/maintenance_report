<?php

$connection = new PDO('mysql:host=127.0.0.1;dbname=laporan_ahs', 'root', '');
$stmt = $connection->query('SELECT id, jenis_pekerjaan, scope, start_time, end_time, downtime_min FROM laporan_harian LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\n============================================ TEST DATA ============================================\n";
echo sprintf("%-5s | %-12s | %-10s | %-19s | %-19s | %-10s\n", 'ID', 'Jenis', 'Scope', 'Start Time', 'End Time', 'Downtime');
echo str_repeat("-", 95) . "\n";

foreach($rows as $row) {
    echo sprintf("%-5d | %-12s | %-10s | %-19s | %-19s | %-10d\n",
        $row['id'],
        $row['jenis_pekerjaan'] ?? 'NULL',
        $row['scope'] ?? 'NULL',
        $row['start_time'] ?? 'NULL',
        $row['end_time'] ?? 'NULL',
        $row['downtime_min'] ?? 0
    );
}

echo "\n✅ Database test successful!\n";
?>
