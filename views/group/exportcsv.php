<?php
$f = fopen('php://memory', 'w');

foreach ($data as $key => $value) {
    fputcsv($f, array(0 => $value), ',');
}

fseek($f, 0);
header('Content-Type: text/csv');
header('Content-Disposition: attachement; filename=export.csv');
fpassthru($f);

?>