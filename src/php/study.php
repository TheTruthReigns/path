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
		case 'getTimeLine':
			$sql = "SELECT * FROM Event";
			$result = mysqli_query($dbconn, $sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = array(
						'Date' => $row['eventDate'],
						'Title' => $row['eventName'],
						'Detail' => $row['eventDetail']
					);
			};
			echo json_encode($data);
			break;

		case 'getYourMentor':
		$schoolID = $_GET['schoolID'];
		$programID = $_GET['programID'];
			$sql = "SELECT * FROM Account a LEFT JOIN DegreeSchool b ON a.degreeSchoolID = b.schoolID WHERE a.schoolID ='$schoolID' AND programID='$programID' AND userGroupID= 2 ";
			$result = mysqli_query($dbconn, $sql);
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = array(
						'accountID' => $row['accountID'],
						'accountName' => $row['accountName'],
						'cgpa' => $row['cgpa'],
						'accountIcon' => $row['accountIcon'],
						'degreeSchoolName' => $row['schoolName'],
						'description' => $row['description']
					);
			}
			echo json_encode($data);
			break;

		case 'getEvents':
		$acID = $_GET['acID'];
		$schoolID = $_GET['subdegreeID'];
			$sql = "SELECT * FROM Activity WHERE subDegree = $schoolID";
			$result = mysqli_query($dbconn, $sql);

			while ($row = mysqli_fetch_assoc($result)) {
				$aID = $row['activityID'];

				$sql2 = "SELECT * FROM ActivityParticipant WHERE accountID = $acID AND activityID = $aID";
				$result2 = mysqli_query($dbconn, $sql2);
					if (mysqli_num_rows($result2) !== 0){
						$bool = true;
					} else {
						$bool = false;
					}

				$sql3 = "SELECT * FROM ActivityParticipant WHERE activityID = $aID";
				$result3 = mysqli_query($dbconn, $sql3);
				$count = mysqli_num_rows($result3);

				$data[] = array(
						'activityID' => $row['activityID'],
						'subject' => $row['subject'],
						'participant' => $row['numOfPe'],
						'startDate' => $row['startDate'],
						'endDate' => $row['endDate'],
						'description' => $row['content'],
						'pic' => $row['pic'],
						'count' => $count,
						'status' => (bool)$row['status'],
						'joined' => $bool 
					);
			}

			echo json_encode($data);
			break;

		case 'joinActivity':
			$evtID = $_GET['evtID'];
			$acID = $_GET['acID'];
			$sql = "INSERT INTO ActivityParticipant (activityID, accountID) VALUES ($evtID, $acID)";
			mysqli_query($dbconn, $sql);
			break;

		case 'formGrp':
			$evtID = $_GET['evtID'];
			$count = 0;
			$records ;

			do{
				$count ++;
				$sql = "SELECT * FROM ActivityParticipant a LEFT JOIN Account b ON a.accountID = b.accountID WHERE activityID = '$evtID' AND grouped = 0 ORDER BY programID,cgpa DESC LIMIT 3";
				$result = mysqli_query($dbconn, $sql);

				while ($row = mysqli_fetch_assoc($result)) {
					$acID = $row['accountID'];
					$sql2 = "INSERT INTO GroupResult (groupID, accountID, eventID) VALUES ($count,$acID,$evtID)";
					mysqli_query($dbconn, $sql2);
					$sql3 = "UPDATE ActivityParticipant SET grouped = 1 WHERE accountID = '$acID'";
					mysqli_query($dbconn, $sql3);
				}
			}while(mysqli_num_rows($result) !== 0);

			$sql4 = "UPDATE Activity SET status = 0 WHERE activityID = '$evtID'";
			mysqli_query($dbconn, $sql4);
			break;

		case 'getGrp':
			$evtID = $_GET['evtID'];
			$acID = $_GET['acID'];
			$sql = "SELECT * FROM GroupResult a LEFT JOIN Account b on a.accountID = b. accountID WHERE groupID = ALL (SELECT groupID FROM GroupResult WHERE accountID = '$acID' AND eventID = '$evtID')" ;
			$result = mysqli_query($dbconn, $sql);
				while ($row = mysqli_fetch_assoc($result)) {
					$data[] = array(
						'username' => $row['userName'],
						'accountIcon' => $row['accountIcon'],
						'cgpa' => $row['cgpa'],
						'email' => $row['email']
					);
				}
			echo json_encode($data);	
	}
}
mysqli_close($dbconn);


?>