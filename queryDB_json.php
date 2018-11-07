<?php
header("Content-Type:application/json; charset=utf-8");
$POST = json_decode(file_get_contents("php://input"));
$input = $POST->v;

$command = 'python3 you2json.py '. $input;

$file = shell_exec($command);
$file = str_replace("\n", "", $file);
$json = file_get_contents("json/" . $file);

$id = str_replace(".json", "", $file);

$command1 = 'python you2title.py '. $input;
$title = shell_exec($command1);


$servername = "localhost";
$username = "webProgramming";
$password = "webProgramming2018";
$dbname = "webProgramming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
    
    $sql = "SELECT * FROM `youtube_cc` WHERE title <> '' and title NOT LIKE '%porn%'";
    $result = $conn->query($sql);

	if (!$result) {
		printf("Errormessage: %s\n", $conn->error);
	}

	$stack = array();


	if ($result->num_rows > 0) {
	// output data of each row
		while($row = $result->fetch_assoc()) {
		  array_push($stack, $row);
		}
	}
	echo json_encode($stack, JSON_UNESCAPED_UNICODE);
}
$conn->close();

//$arr = array('input'=> $input, 'id'=> $id, 'title'=> $title, 'file' => $file, 'json'=>$json);
//echo json_encode($arr);
?>