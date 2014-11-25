<?php
include('ganon.php');

$URL = "http://www.swift-code.com/australia/page/";

$no = 1;
$cities = array();
$results = array();

$servername = "localhost";
$username = "ngo_van_thang";
$password = "ngo_van_thang";
$dbname = "ngo_van_thang";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

for ($i=1; $i<=7; $i++) {
    echo $URL . $i."\n";
    $html = file_get_dom($URL . $i. "/");

    foreach ($html('table#swift tr:gt(0)') as $element) {
        $element2 = $element("td");
        $bank = trim($element2[1]->getPlainText());
        $city = trim($element2[2]->getPlainText());
        $branch = trim($element2[3]->getPlainText());
        $code = trim($element2[4]->getPlainText());

        if (strlen($city) == 0) {
            continue;
        }

        // echo $bank. " - ". $city. " - ". $branch. "\n";
        $flag = false;
        for ($j = 0; $j<$no; $j++ ) {
            if (@$cities[$j] == $city) {
                $flag = true;
                break;
            }
        }

        if (!$flag) {
            $cities[$no] = $city;
            $no++;
        }

        // insert data into results array
        $results[$city][] = array($bank, $branch, $code);
    }
}

echo "============= CITY =============== \n";
for ($i=1; $i<=count($cities); $i++) {
    $id = $i;
    echo $cities[$i]."\n";
    echo "--------------\n";
    
    $sql = "INSERT INTO city (id, city) VALUES ($id, '$cities[$i]')";
    $conn->query($sql);

    foreach ($results[$cities[$i]] as $value) {
        $bank = $value[0];
        $branch = $value[1];
        $code = $value[2];

        $sql = "INSERT INTO bank (city_id, bank_name, branch_name, swift_code) VALUES ($id, '$bank', '$branch', '$code')";
        $conn->query($sql);
    }

    echo "\n";
}

?>