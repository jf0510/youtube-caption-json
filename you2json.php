<?php
header("Content-Type:application/json; charset=utf-8");
$POST = json_decode(file_get_contents("php://input"));
$input = $POST->v;

$command = 'python3 you2json.py '. $input;

$file = shell_exec($command);
$file = str_replace("\n", "", $file);
$json = file_get_contents("json/" . $file);

$video_id = str_replace(".json", "", $file);

//$command1 = 'python you2title.py '. $input;
//$title = shell_exec($command1);
function youtube_title($id) {
	// $id = 'YOUTUBE_ID';
	// returns a single line of JSON that contains the video title. Not a giant request.
	$videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=AIzaSyC8O_VAWWHLHbStWbX-2wNzr8FrRTkI16w&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
	// despite @ suppress, it will be false if it fails
	if ($videoTitle) {
		$json = json_decode($videoTitle, true);
		return $json['items'][0]['snippet']['title'];
	} else {
		return false;
	}
}

$title = youtube_title($video_id);
$title = str_replace("'", "\'", $title);
/*
$servername = "localhost";
$username = "webProgramming";
$password = "webProgramming2018";
$dbname = "webProgramming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{
    if ($video_id != ""){
        $sql = "INSERT INTO youtube_cc(video_id, title) VALUES('$video_id' , '$title')";
        if ($conn->query($sql) === TRUE) {
            $status = "success!";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
            $status = "failed...";
        }
    }
}
$conn->close();
*/
$status = "success!";
$arr = array('input'=> $input, 'video_id'=> $video_id, 'title'=> $title, 'file' => $file, 'json'=>$json, 'status'=>$status);
echo json_encode($arr);
?>