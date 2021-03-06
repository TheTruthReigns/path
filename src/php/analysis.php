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
		$action = $_GET['action'];
    	$data = Array();
		switch ($action) {
			case 'getSucessCase':
				$sql = "SELECT * FROM School";
                $result = mysqli_query($dbconn, $sql);
				while ($row = mysqli_fetch_assoc($result)) {
					$i = 0;
					$CaseListschoolID = $row['schoolID'];
					$case = Array();
					$sql2 = "SELECT 
                                *
                            FROM `Case` a
                            LEFT JOIN School b ON a.schoolID = b.schoolID
                            LEFT JOIN Program c ON c.schoolID = b.schoolID AND a.programID = c.programID
                            LEFT JOIN ProgramType d ON c.programTypeID = d.programTypeID
                            WHERE a.schoolID = $CaseListschoolID
                            ORDER BY a.ProgramID,a.offerDate DESC";
                    $result2 = mysqli_query($dbconn, $sql2);
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                    	$i++;
                    	if($row2['fullcert'] == true){
	                        $fullcert = "YES";
	                     }else{
	                        $fullcert = "NO";
	                     }
                    	$offerProgramID = $row2['offerProgramID'];
                        $offerSchoolID = $row2['offerSchoolID'];
                        $sql3 = "SELECT 
                        				* FROM DegreeSchool a 
                                  LEFT JOIN DegreeProgram b ON a.schoolID = b.schoolID 
                               	  WHERE b.schoolID = $offerSchoolID AND b.programID = $offerProgramID";
                        $result3 = mysqli_query($dbconn, $sql3);
                        while ($row3 = mysqli_fetch_assoc($result3)) {

                        	$contents[] = array(
                        		'Category' => $row2['programTypeName'],
                        		'offerDate' => $row2['offerDate'],
                        		'ProgrammeName' => $row2['programName'],
                        		'OfferUniversity' => $row3['schoolName'],
                        		'OfferProgramme' => $row3['programName'],
                        		'DSEFullCert' => $fullcert,
                        		'sem1GPA' => $row2['sem1GPA'],
                        		'sem2GPA' => $row2['sem2GPA'],
                        		'sem3GPA' => $row2['sem3GPA'],
                        		'sem4GPA' => $row2['sem4GPA'],
                        		'CGPA' => $row2['cGPA'],
                        		'itelts' => $row2['ielts'],
                        		'offerType' => $row2['offerType']
                        	);
                        };
                    };
					$data[] = array(
									'SchoolName' => $row['schoolName'],
                                    'SchoolID' => $row['schoolID'],
									'contents'=> $contents,
									);
				}
				echo json_encode($data);
				break;

            case 'createCase':
                $schoolID = $_GET['schoolID'];
                $programID = $_GET['programID'];
                $sem1GPA = $_GET['sem1GPA'];
                $sem2GPA = $_GET['sem2GPA'];
                $sem3GPA = $_GET['sem3GPA'];
                $sem4GPA = $_GET['sem4GPA'];
                $ielts = $_GET['ielts'];
                $offerDate = $_GET['offerDate'];
                $dse = (bool) $_GET['dse'];
                $subSchoolID = $_GET['subSchoolID'];
                $subProgramID = $_GET['subProgramID'];
                $accountID = $_GET['accountID'];

                $sql ="INSERT INTO `Case` (accountID,schoolID,programID,sem1GPA,sem2GPA,sem3GPA,sem4GPA,offerProgramID,offerSchoolID,offerDate,ielts,fullcert) VALUES ($accountID,$subSchoolID,$subProgramID,$sem1GPA,$sem2GPA,$sem3GPA,$sem4GPA,$programID,$schoolID,'$offerDate',$ielts,$dse)";
                $result = mysqli_query($dbconn, $sql);

                break;			

		}
		mysqli_close($dbconn);
	}
?>
