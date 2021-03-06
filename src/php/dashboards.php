<?php
require ("dbconn.php");
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$dbconn = dbconn();
if (!$dbconn) {
    die("dbconnection failed: " . mysqli_dbconnect_error());
} 
else {
    $data = Array();
    $action = $_GET['action'];
    $options = array(
                'maintainAspectRatio'=> true,
                'datasetFill'=> false,
                'responsive'=> true,
                'scaleShowGridLines'=> false,
                'bezierCurve'=> true,
                'scaleFontColor'=> '#ffffff',
                'scaleFontSize'=> 16
            );
    switch ($action) {
        case 'getOfferEachYear':
            $sql = "SELECT 
                        COUNT(*) AS yearOfferCount,
                        YEAR(offerDate) As year
                    FROM `Case` a
                    GROUP BY YEAR(offerDate)";
            $result = mysqli_query($dbconn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $yearOfferCount[] = $row['yearOfferCount'];
                $year[] = $row['year'];
            }
            $yearOfferCount = array($yearOfferCount);
            $data = array(
                'data' => $yearOfferCount,
                'labels' => $year,
                'series' => ["offer"],
                'colors' => "#fff",
                'options'=> $options,
            );
            echo json_encode($data);
            break;
        case 'getCurrentOfferBySchool':
            $sql = "SELECT 
                    COUNT(*) AS schoolOfferCount,
                    schoolName
            FROM `Case` a
            LEFT JOIN DegreeSchool b ON a.offerSchoolID = b.SchoolID
            WHERE YEAR(offerDate) = YEAR(NOW())
            GROUP BY offerSchoolID";
            $result = mysqli_query($dbconn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $schoolOfferCountArray[] = $row['schoolOfferCount'];
                $schoolName[] = $row['schoolName'];
            }
            $data = array(
                'data' => $schoolOfferCountArray,
                'labels' => $schoolName,
                'series' => ["offer"],
                'colors' => "#fff",
                'options'=> $options,
            );
            echo json_encode($data);
            break;
        case 'getCurrentSchoolOfferByType':
                $sql = "SELECT * FROM ProgramType";
                $result = mysqli_query($dbconn, $sql);
                $chart = array("title" => "Offer In Your College In Current Year");
                $dataArray[] = array("Category","AD","HD");
                while ($row = mysqli_fetch_assoc($result)) {
                    $HDofferCount = 0;
                    $ADofferCount = 0;
                    $programTypeID = $row['programTypeID'] ;
                    $sql2 = "SELECT COUNT(*) As ADofferCount  FROM `Case` a
                             LEFT JOIN School b ON a.schoolID = b.schoolID
                             LEFT JOIN Program c ON c.schoolID = b.schoolID AND a.programID = c.programID
                             WHERE c.programTypeID = '$programTypeID' AND a.schoolID = 1 AND c.educationType = 'AD'";
                    $result2 = mysqli_query($dbconn, $sql2);
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        $ADofferCount += $row2['ADofferCount'];
                    };

                    $sql3 = "SELECT COUNT(*) As HDofferCount  FROM `Case` a
                             LEFT JOIN School b ON a.schoolID = b.schoolID
                             LEFT JOIN Program c ON c.schoolID = b.schoolID AND a.programID = c.programID
                             WHERE c.programTypeID = '$programTypeID' AND a.schoolID = 1 AND c.educationType = 'HD'";
                    $result3 = mysqli_query($dbconn, $sql3);
                    while ($row3 = mysqli_fetch_assoc($result3)) {
                        $HDofferCount += $row3['HDofferCount'];
                    };
                    $dataArray[] = array($row['programTypeName'],$ADofferCount,$HDofferCount);
                }
                $options = array(
                    "bars"=>"vertical",
                    "width"=>"100%",
                    "height"=>"500");
                $data = array(
                'type' => "Bar",
                'data' => $dataArray,
                'options'=> $options,
                );
                echo json_encode($data);  
            break;
        case 'getOfferEachYearBySchool':
            $sql2 = "SELECT Distinct(YEAR(offerDate)) As Year FROM `Case` a ORDER BY offerDate";
            $result2 = mysqli_query($dbconn, $sql2);
            while ($row2 = mysqli_fetch_assoc($result2)) {
                $yearArray[] = $row2['Year'];
            }
            $sql = "SELECT * FROM School";
            $result = mysqli_query($dbconn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $yearOfferCount = Array();
                $schoolName[] = $row['schoolName'];
                $schoolID = $row['schoolID'];
                $sql2 = "SELECT Distinct(YEAR(offerDate)) As Year FROM `Case` a ORDER BY offerDate";
                $result2 = mysqli_query($dbconn, $sql2);
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $year = $row2['Year'];
                    $sql3 = "SELECT 
                                    COUNT(*) AS yearOfferCount
                            FROM `Case` a
                            WHERE schoolID = '$schoolID' AND Year(offerDate) = '$year'
                            GROUP BY schoolID";
                    $result3 = mysqli_query($dbconn, $sql3);
                    while ($row3 = mysqli_fetch_assoc($result3)) {
                        $yearOfferCount[] = $row3['yearOfferCount'];
                    }
                }
                $yearOfferArray[] = $yearOfferCount;
            }
            $data = array(
                'data' => $yearOfferArray,
                'labels' => $yearArray,
                'series' => $schoolName,
                'colors' => "#fff",
                'options'=> $options,
            );
            echo json_encode($data);
            break;
        case 'getDailyMessage':
            $sql = "SELECT * FROM Message WHERE messageDate BETWEEN (NOW() - INTERVAL 3 MONTH) AND NOW()";
            $result = mysqli_query($dbconn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = array(
                    'body' => $row['messageBody'],
                    'bg' => 'background:url('.$row['bg'].') no-repeat center center ;'
                );
            }
            echo json_encode($data);
            break;
    }
}
mysqli_close($dbconn);
?>
