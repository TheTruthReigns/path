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
		case 'checkLogin':
		$accountName = $_GET['AccountName'];
		$password = $_GET['AccountPassword'];
		$sql = "SELECT * FROM Account WHERE accountName = '$accountName'";
		$result = mysqli_query($dbconn, $sql);
		while ($row = mysqli_fetch_assoc($result)) {
			$accountID = $row['accountID'];
			$userGroupID = $row['userGroupID'];
			$userName = $row['userName'];
			$accountIcon = $row['accountIcon'];
			$schoolID = $row['schoolID'];
			$programID = $row['programID'];
			$year = $row['year'];
			$failCount = $row['failCount'];
			$cgpa = $row['cgpa'];
			$mentor = $row['mentor'];
			$degreeID = $row['degreeSchoolID'];
			$degreeProgramID = $row['degreeprogramID'];
			$loginStatus = $row['loginStatus'];
			$sql2 = "SELECT * FROM AccountSkills WHERE accountID = '$accountID'";
			$result2 = mysqli_query($dbconn, $sql2);
			$skills = Array();
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$skills[] = $row2['skill'];
			}
			$sql2 = "SELECT * FROM School WHERE schoolID = '$schoolID'";
			$result2 = mysqli_query($dbconn, $sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$schoolName = $row2['schoolName'];
			}
			$sql2 = "SELECT * FROM DegreeSchool WHERE schoolID = '$degreeID'";
			$result2 = mysqli_query($dbconn, $sql2);
			if (mysqli_num_rows($result2) == 0){
				$degreeName = 'N/A';
			} else {
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$degreeName = $row2['schoolName'];
				}
			}
			$sql2 = "SELECT * FROM DegreeProgram WHERE schoolID = '$degreeID' AND programID = '$degreeProgramID'";
			$result2 = mysqli_query($dbconn, $sql2);
			if (mysqli_num_rows($result2) == 0){
				$degreeProgramName = 'N/A';
			} else {
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$degreeProgramName = $row2['programName'];
				}
			}
			$sql2 = "SELECT * FROM Program WHERE schoolID = '$schoolID' AND programID = '$programID'";
			$result2 = mysqli_query($dbconn, $sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$programName = $row2['programName'];
			}
			$sql2 = "SELECT * FROM UserGroup WHERE userGroupID ='$userGroupID'";
			$result2 = mysqli_query($dbconn, $sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$userGroupName = $row2['userGroupName'];
			}
			$sql2 = "SELECT * , a.createDt As notificationTime
			FROM AccountNotification a
			LEFT JOIN Account b ON a.senderAccountID = b.accountID
			LEFT JOIN Notification c ON a.notificationTypeID = c.notificaionTypeID
			WHERE a.receiverAccountID = '$accountID'";
			$result2 = mysqli_query($dbconn, $sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$senderUsername = $row2['userName'];
				$notificationMessage = $row2['notificationMessage'];
				$notificationTime = $row2['notificationTime'];
				$title = $senderUsername." ".$notificationMessage;
				$notifications[] = array(
					'notificationID' => $row2['notificationID'],
					'title' => $title, 
					'date' => $notificationTime,
					'path' => $row2['path'],
					'read' => $row2['read'],
					);
			}
			$notificationGroup[] = array('name' => 'Notification' , 'notifications'=> $notifications );
			if (password_verify($password, $row['password']) && $loginStatus == 0 && $failCount < 6){
				$sql3 = "INSERT INTO Log (logType,createDt,logDesc) VALUES ('Login',NOW(),'Login Sucess')";
				mysqli_query($dbconn, $sql3);
				$sql4 = "UPDATE Account SET loginStatus = 1 WHERE accountID = $accountID";
				mysqli_query($dbconn, $sql4);
				$data= array(
					'status' => "true", 
					'accountID'=> $accountID,
					'displayName'=> $userName,
					'username' => $userName,
					'avatar'=> $accountIcon,
					'schoolID' => $schoolID,
					'programID' => $programID,
					'schoolName' => $schoolName,
					'programName' => $programName,
					'degreeName' => $degreeName,
					'degreeProgramName' => $degreeProgramName,
					'year' => $year,
					'cgpa' => $cgpa,
					'roles' => [$userGroupName],
					'skills'=> $skills,
					'notificationGroup' => $notificationGroup,
					'mentor' => $mentor
					);
			}
			else{
				$sql5 = "UPDATE Account SET failCount = $failCount + 1 WHERE accountName = '$accountName'";
				$sql3 = "INSERT INTO Log (logType,createDt,logDesc) VALUES ('Login',NOW(),'Login Fail')";
				mysqli_query($dbconn, $sql3);
				if ( $loginStatus == 1 ){
					$data= array('status' => "occupied");
				} else if ( $failCount == 6 ){
					$data= array('status' => "locked");
				} else {
					mysqli_query($dbconn, $sql5);
					$chance = 5 - $failCount;
					$data= array('status' => "false", 'chance' => $chance);
				}
			}
			echo json_encode($data);
		}
		break;

		case 'getRole':
		$sql = "SELECT * FROM UserGroup";
		$result = mysqli_query($dbconn, $sql);
		while ($row = mysqli_fetch_assoc($result)) {
			$roles = array();
			$userGroupID = $row['userGroupID'];
			$userGroupName = $row['userGroupName'];
			$sql2 = "SELECT * FROM UserRole a LEFT JOIN Permission b ON a.userRoleID = b.permissionID WHERE userGroupID = '$userGroupID' AND own = 1";
			$result2 = mysqli_query($dbconn, $sql2);
			while ($row2 = mysqli_fetch_assoc($result2)) {

				$roles[] = $row2['permissionName'];
			}
			$UserRoles[$userGroupName] = $roles;
		}
		$sql2 = "SELECT * FROM Permission";
		$result2 = mysqli_query($dbconn, $sql2);
		while ($row2 = mysqli_fetch_assoc($result2)) {
			$permissions[] = $row2['permissionName'];
		}
		$data = array('UserRoles'=>$UserRoles,'Permissions'=> $permissions);
		echo json_encode($data);
		break;

		case 'updatePassword':
		$AccountID = $_GET['AccountID'];
		$AccountPassword = $_GET['AccountPassword'];
		$AccountPassword = password_hash($AccountPassword, PASSWORD_DEFAULT);
		$sql = "UPDATE Account SET password = '$AccountPassword' WHERE accountID = '$AccountID'";
		echo $sql;
		$result = mysqli_query($dbconn, $sql);
		break;

		case 'checkPassword':
		$AccountID = $_GET['AccountID'];
		$AccountPassword = $_GET['AccountPassword'];	
		$sql = "SELECT * FROM Account WHERE accountID = '$AccountID'";
		$result = mysqli_query($dbconn, $sql);	  
		while ($row = mysqli_fetch_assoc($result)) {
			if (password_verify($AccountPassword, $row['password'])){
				$data= array(
					'status' => "true", 
					);
			}
			else{
				$data= array('status' => "false");
			}
			echo json_encode($data);
		}  
		break;

		case 'updatePersonalInfo':
		$AccountID = $_GET['AccountID'];
		$userName = $_GET['userName'];
		$cgpa = $_GET['cgpa'];
		$myData = $_GET['myData'];
		$myData2 = explode(",", $myData);
		$sql = "UPDATE Account SET userName = '$userName', cgpa = '$cgpa' WHERE accountID = '$AccountID'";
		$result = mysqli_query($dbconn, $sql);
		$sql2 = "DELETE FROM AccountSkills WHERE accountID = '$AccountID'";
		$result = mysqli_query($dbconn, $sql2);
		foreach($myData2 as $data){
			$sql3 = "INSERT INTO AccountSkills (accountID, skill) VALUES ('$AccountID','$data')";
			$result = mysqli_query($dbconn, $sql3);
		}
		break;   

		case 'uploadIcon':
		$filename = $_FILES['file']['name'];
		$meta = $_POST;
		$destination = $meta['targetPath'];
		$userID = $meta['userID'];
		$today = date("Y-m-j-H-i-s");
		$temp = explode(".", $_FILES["file"]["name"]);
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if($ext == 'jpg' || $ext == 'png'){
		$newfilename =  $userID . "_" . $today . '.' . end($temp);
		$path = 'http://unicomhk.net/path2/img/avatar/' . $newfilename;
		move_uploaded_file($_FILES["file"]["tmp_name"], $destination . $newfilename);
		$sql = "UPDATE Account SET accountIcon = '$path' WHERE accountID = '$userID'";
		$result = mysqli_query($dbconn, $sql);
		echo json_encode($path);
		}
		break;

		case 'setReadNotification':
		$notificationID = $_GET['notificationID'];
		$sql = "UPDATE AccountNotification SET `Read` = true WHERE notificationID = '$notificationID'";
		$result = mysqli_query($dbconn, $sql);
		break;

		case 'logout':
		$accountID = $_GET['uID'];
		$sql = "UPDATE Account SET loginStatus = '0', failCount = '0' WHERE accountID = $accountID";
		mysqli_query($dbconn, $sql);
		break;

	}
}
mysqli_close($dbconn);



?>
