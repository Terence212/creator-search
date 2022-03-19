<?php

$base_url = 'https://youtube.googleapis.com/youtube/v3/';
$key = 'AIzaSyCU_yWBimBh1Uq5xh7o2o3AgHLZG6GwMws';
$channelImage = null;
$channelName = null;
$channelDesc = null;
$channelId = null;

// My channel ID
$test_channel_id = 'UCSxk0Hyq-6OGWrcNxL6HzxA';

//create & initialize a curl session
$curl = curl_init();

// example channel name
// https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&maxResults=15&q=TerryV212&key=AIzaSyCU_yWBimBh1Uq5xh7o2o3AgHLZG6GwMws

// example channel
// https://youtube.googleapis.com/youtube/v3/search?part=snippet&order=date&channelId=UCSxk0Hyq-6OGWrcNxL6HzxA&type=video&key=AIzaSyCU_yWBimBh1Uq5xh7o2o3AgHLZG6GwMws

// example video
// https://youtube.googleapis.com/youtube/v3/videos?part=snippet%2Cplayer&id=Yc4LRB-W04c&key=AIzaSyCU_yWBimBh1Uq5xh7o2o3AgHLZG6GwMws


if ( !empty($_POST['channelName'])) {

    $searchName = $_POST['channelName'];
    $url = $base_url.'search?part=snippet&type=channel&q='.$searchName.'&key='.$key;

    // set our url with curl_setopt() 
    curl_setopt($curl, CURLOPT_URL, "$url");

    // return the transfer as a string, also with setopt()
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // curl_exec() executes the started curl session
    // $output contains the output string
    $channel_info = json_decode(curl_exec($curl), true);

    if ($channel_info) {

        $channelImage = $channel_info['items'][0]['snippet']['thumbnails']['default']['url'];
        $channelName = $channel_info['items'][0]['snippet']['channelTitle'];
        $channelId = $channel_info['items'][0]['snippet']['channelId'];

    }

    echo json_encode(
        array(
            'channelData' => $channel_info,
            'channelImage' => $channelImage,
            'channelName' => $channelName,
            'channelId' => $channelId
        )
    );

}

if ( !empty($_POST['channelId'])) {

    $channelId = $_POST['channelId'];
    $url = $base_url.'search?part=snippet&order=date&channelId='.$channelId.'&type=video&key='.$key;

    // set our url with curl_setopt() 
    curl_setopt($curl, CURLOPT_URL, "$url");

    // return the transfer as a string, also with setopt()
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // curl_exec() executes the started curl session
    // $output contains the output string
    $channel_search = json_decode(curl_exec($curl), true);

    //most recent video id
    $videoId = $channel_search["items"][0]["id"]["videoId"];

    $url = $base_url.'videos?part=snippet%2Cplayer&id='. $videoId .'&key='.$key;

    curl_setopt($curl, CURLOPT_URL, "$url");

    // return the transfer as a string, also with setopt()
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // curl_exec() executes the started curl session
    // $output contains the output string
    $video_info = json_decode(curl_exec($curl), true);

    $videoEmbed = $video_info['items'][0]['player']['embedHtml'];
    $videoTitle = $video_info['items'][0]['snippet']['title'];
    $videoDesc = $video_info['items'][0]['snippet']['description'];
    $videoLive = $video_info['items'][0]['snippet']['liveBroadcastContent'];

    echo json_encode(
        array(
            'recentVideos' => $channel_search,
            'video' => $video_info,
            'videoTitle' => $videoTitle,
            'videoDesc' => $videoDesc,
            'videoLive' => $videoLive,
            'videoEmbed' => $videoEmbed,
            'channelId' => $channelId
        )
    );
    
}

?>