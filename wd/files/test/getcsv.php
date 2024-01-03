<?php

$file = 'https://cdn.sale-storm.com/wd/files/exp_price_m.csv';

$row = 0;
if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
        $total_lines++;
        $num = count($data);
        $row++;
if (($data[0] == '6PK2584')||($data[0] == 'GT20150')||($data[0] == 'CLKH30')) {
//6PK2584
//CLKH30
//GT20150
        echo "<p> $num полей в строке $row: <br /></p>\n";
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
        echo "<p>=========================== Конец строки ================<br /></p>\n";
}
    }
    fclose($handle);
}


echo "Total Lines: $total_lines.\n";

?>