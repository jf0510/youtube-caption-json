<?php
header("Content-Type:application/json; charset=utf-8");
$POST = json_decode(file_get_contents("php://input"));
$video_id = $POST->v;

function youtube_Timetext($id) {
    $url = "http://video.google.com/timedtext?lang=en&v=" . $id;
    $video_Timetext = file_get_contents($url);

    if($video_Timetext){
        $subtitles = array();
        $index = 1;
        $xml = simplexml_load_string($video_Timetext);
        $xml_json = json_encode($xml);
        $xml_array = json_decode($xml_json,TRUE);

        foreach($xml->text as $line){
            $start = floatval($line['start']) * 1000;
            $end = $start + floatval($line['dur']) * 1000; 
            $subtitle = array("index"=> $index, "start_time"=> $start, "end_time" => $end, "text" => $xml_array['text'][$index-1]);
            array_push($subtitles, $subtitle);
            $index += 1;
        }

        $subtitle_json = json_encode($subtitles);
        $subtitle_array = json_decode($subtitle_json,TRUE);
        return $subtitle_array;
    } else {
        return false;
    }
}

function youtube_Title($id) {
	// $id = 'YOUTUBE_ID';
    // returns a single line of JSON that contains the video title.
    $url  =  "https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=AIzaSyC8O_VAWWHLHbStWbX-2wNzr8FrRTkI16w&fields=items(id,snippet(title),statistics)&part=snippet,statistics"; 
    $ch  = curl_init();  
    curl_setopt( $ch , CURLOPT_URL, $url );  
    curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST, false);  
    curl_setopt( $ch , CURLOPT_RETURNTRANSFER, 1);  
    $videoTitle  = curl_exec( $ch );  
	if ($videoTitle) {
		$json = json_decode($videoTitle, true);
		return $json['items'][0]['snippet']['title'];
	} else {
		return false;
	}
}

$title = youtube_Title($video_id);
$timetext = youtube_Timetext($video_id);

$filename = 'json/'.$video_id.'.json';
$fp = fopen($filename, 'w');
fwrite($fp, json_encode($timetext, JSON_PRETTY_PRINT));
fclose($fp);

$status = "Success";
$arr = array('video_id'=> $video_id, 'title'=> $title, 'file' => $video_id.'.json', 'json'=>json_encode($timetext, JSON_PRETTY_PRINT), 'status'=>$status);
echo json_encode($arr);

// var_dump($title);
// var_dump($timetext);
// var_dump(json_encode($timetext, JSON_PRETTY_PRINT));

/*$xmlstring = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<transcript>
<text start="7.785" dur="1.519">In the world of math,</text>
<text start="9.304" dur="4.08">
many strange results are possible when we change the rules.
</text>
<text start="13.384" dur="3.842">
But there��s one rule that most of us have been warned not to break:
</text>
<text start="17.226" dur="2.379">don��t divide by zero.</text>
<text start="19.605" dur="2.979">
How can the simple combination of an everyday number
</text>
<text start="22.584" dur="3.879">and a basic operation cause such problems?</text>
<text start="26.463" dur="3.23">Normally, dividing by smaller and smaller numbers</text>
<text start="29.693" dur="2.628">gives you bigger and bigger answers.</text>
<text start="32.321" dur="2.523">Ten divided by two is five,</text>
<text start="34.844" dur="1.599">by one is ten,</text>
<text start="36.443" dur="2.579">by one-millionth is 10 million,</text>
<text start="39.022" dur="0.958">and so on.</text>
<text start="39.98" dur="2.311">So it seems like if you divide by numbers</text>
<text start="42.291" dur="2.609">that keep shrinking all the way down to zero,</text>
<text start="44.9" dur="3.161">
the answer will grow to the largest thing possible.
</text>
<text start="48.061" dur="4.662">
Then, isn��t the answer to 10 divided by zero actually infinity?
</text>
<text start="52.723" dur="1.96">That may sound plausible.</text>
<text start="54.683" dur="3.05">But all we really know is that if we divide 10</text>
<text start="57.733" dur="2.941">by a number that tends towards zero,</text>
<text start="60.674" dur="3.06">the answer tends towards infinity.</text>
<text start="63.734" dur="4.13">
And that��s not the same thing as saying that 10 divided by zero
</text>
<text start="67.864" dur="2.65">is equal to infinity.</text>
<text start="70.514" dur="1.319">Why not?</text>
<text start="71.833" dur="4.389">
Well, let��s take a closer look at what division really means.
</text>
<text start="76.222" dur="2.352">Ten divided by two could mean,</text>
<text start="78.574" dur="4.097">
&quot;How many times must we add two together to make 10,��
</text>
<text start="82.671" dur="3.442">or, ��two times what equals 10?��</text>
<text start="86.113" dur="4.34">
Dividing by a number is essentially the reverse of multiplying by it,
</text>
<text start="90.453" dur="1.948">in the following way:</text>
<text start="92.401" dur="3.022">if we multiply any number by a given number x,</text>
<text start="95.423" dur="4.23">
we can ask if there��s a new number we can multiply by afterwards
</text>
<text start="99.653" dur="2.633">to get back to where we started.</text>
<text start="102.286" dur="5.058">
If there is, the new number is called the multiplicative inverse of x.
</text>
<text start="107.344" dur="4.101">
For example, if you multiply three by two to get six,
</text>
<text start="111.445" dur="4.119">
you can then multiply by one-half to get back to three.
</text>
<text start="115.564" dur="3.83">So the multiplicative inverse of two is one-half,</text>
<text start="119.394" dur="4.57">
and the multiplicative inverse of 10 is one-tenth.
</text>
<text start="123.964" dur="5.27">
As you might notice, the product of any number and its multiplicative inverse
</text>
<text start="129.234" dur="2.03">is always one.</text>
<text start="131.264" dur="2.199">If we want to divide by zero,</text>
<text start="133.463" dur="2.38">we need to find its multiplicative inverse,</text>
<text start="135.843" dur="3.301">which should be one over zero.</text>
<text start="139.144" dur="5.828">
This would have to be such a number that multiplying it by zero would give one.
</text>
<text start="144.972" dur="4.171">
But because anything multiplied by zero is still zero,
</text>
<text start="149.143" dur="2.413">such a number is impossible,</text>
<text start="151.556" dur="3.286">so zero has no multiplicative inverse.</text>
<text start="154.842" dur="2.501">Does that really settle things, though?</text>
<text start="157.343" dur="3.64">
After all, mathematicians have broken rules before.
</text>
<text start="160.983" dur="1.73">For example, for a long time,</text>
<text start="162.713" dur="4.061">
there was no such thing as taking the square root of negative numbers.
</text>
<text start="166.774" dur="4.087">
But then mathematicians defined the square root of negative one
</text>
<text start="170.861" dur="2.423">as a new number called i,</text>
<text start="173.284" dur="4.519">
opening up a whole new mathematical world of complex numbers.
</text>
<text start="177.803" dur="1.442">So if they can do that,</text>
<text start="179.245" dur="1.958">couldn��t we just make up a new rule,</text>
<text start="181.203" dur="4.061">
say, that the symbol infinity means one over zero,
</text>
<text start="185.264" dur="2.279">and see what happens?</text>
<text start="187.543" dur="1.05">Let&#39;s try it,</text>
<text start="188.593" dur="3.13">
imagining we don��t know anything about infinity already.
</text>
<text start="191.723" dur="2.569">
Based on the definition of a multiplicative inverse,
</text>
<text start="194.292" dur="4.182">zero times infinity must be equal to one.</text>
<text start="198.474" dur="6.056">
That means zero times infinity plus zero times infinity should equal two.
</text>
<text start="204.53" dur="1.957">Now, by the distributive property,</text>
<text start="206.487" dur="2.901">the left side of the equation can be rearranged</text>
<text start="209.388" dur="3.231">to zero plus zero times infinity.</text>
<text start="212.619" dur="3.569">And since zero plus zero is definitely zero,</text>
<text start="216.188" dur="3.861">that reduces down to zero times infinity.</text>
<text start="220.049" dur="3.668">
Unfortunately, we��ve already defined this as equal to one,
</text>
<text start="223.717" dur="4.636">
while the other side of the equation is still telling us it��s equal to two.
</text>
<text start="228.353" dur="2.885">So, one equals two.</text>
<text start="231.238" dur="3.226">Oddly enough, that&#39;s not necessarily wrong;</text>
<text start="234.464" dur="3.668">
it&#39;s just not true in our normal world of numbers.
</text>
<text start="238.132" dur="2.582">
There��s still a way it could be mathematically valid,
</text>
<text start="240.714" dur="4.507">
if one, two, and every other number were equal to zero.
</text>
<text start="245.221" dur="2.493">But having infinity equal to zero</text>
<text start="247.714" dur="5.17">
is ultimately not all that useful to mathematicians, or anyone else.
</text>
<text start="252.884" dur="3.23">
There actually is something called the Riemann sphere
</text>
<text start="256.114" dur="3.295">
that involves dividing by zero by a different method,
</text>
<text start="259.409" dur="2.364">but that��s a story for another day.</text>
<text start="261.773" dur="4.192">
In the meantime, dividing by zero in the most obvious way
</text>
<text start="265.965" dur="1.768">doesn��t work out so great.</text>
<text start="267.733" dur="2.941">
But that shouldn��t stop us from living dangerously
</text>
<text start="270.674" dur="2.931">
and experimenting with breaking mathematical rules
</text>
<text start="273.605" dur="3.878">
to see if we can invent fun, new worlds to explore.
</text>
</transcript>
XML;

$xml = simplexml_load_string($xmlstring);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
var_dump($array);*/

//echo $xml->text[1]['start'];
?>