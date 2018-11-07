<?php
$video_id = $_GET['video_id'];

function youtube_Title($id) {
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

$id = "vfGLj-tiG5g";
$title = youtube_Title($video_id);

echo $title;

?>
