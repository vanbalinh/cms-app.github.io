<?php
include_once __DIR__ . './../.sql/get_content.php';
$content = getContent();
$value = "";
$mysqli = new mysqli("localhost", "root", "", "t1");
echo "Vui lòng chờ...";
foreach ($content as $f) {
    $sql = "";
    $value .= "-- START " . $f->name . "
    ";
    $sql .= "-- START " . $f->name . "
    ";
    foreach ($f->data as $c) {
        $value .= "" . $c . "
        ";
        $sql .= "" . $c . "
        ";
    }
    $value .= "-- END " . $f->name . ".sql
    ";
    $sql .= "-- END " . $f->name . ".sql
    ";
    $value .= "-- =====================================*****=====================================
    ";
    $sql .= "-- =====================================*****=====================================
    ";
    // $mysqli->multi_query($sql);
}
// echo "Vui lòng chờ...";
mysqli_report(MYSQLI_REPORT_ERROR  |  MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("localhost", "root", "", "t1");

$mysqli->multi_query($value);

// do {
// //     if ($result = $mysqli->store_result()) {
// //         $result->free();
// //     }
//     if(true){

//     }
// } while ($mysqli->next_result());

echo "Xong";
