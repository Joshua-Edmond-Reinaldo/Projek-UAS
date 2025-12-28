<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Hanya admin yang boleh backup
if ($_SESSION['level'] != 'admin') {
    die("Akses Ditolak: Hanya admin yang boleh melakukan backup database.");
}

require "koneksi.php";

// Konfigurasi Backup
$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$return = "";

foreach ($tables as $table) {
    $result = $conn->query("SELECT * FROM " . $table);
    $num_fields = $result->field_count;

    $return .= "DROP TABLE IF EXISTS " . $table . ";";
    $row2 = $conn->query("SHOW CREATE TABLE " . $table)->fetch_row();
    $return .= "\n\n" . $row2[1] . ";\n\n";

    for ($i = 0; $i < $num_fields; $i++) {
        while ($row = $result->fetch_row()) {
            $return .= "INSERT INTO " . $table . " VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                $row[$j] = $conn->real_escape_string($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                if (isset($row[$j])) {
                    $return .= '"' . $row[$j] . '"';
                } else {
                    $return .= '""';
                }
                if ($j < ($num_fields - 1)) {
                    $return .= ',';
                }
            }
            $return .= ");\n";
        }
    }
    $return .= "\n\n\n";
}


// Download file
$filename = 'db-backup-' . date('Y-m-d-H-i-s') . '.sql';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($return));

echo $return;

exit;
?>