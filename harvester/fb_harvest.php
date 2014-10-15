<?php

/**
* fb_harvest.php?nor=10&group=1
* nor = NumberOfRecords to harvest from facebook. Default is 10
* group: 
*   1 = Gamle København
*   2 = Vesterbro Billeder 
*/

header('Content-Type: application/json');
include_once('./autoload.php');
include_once('./Bootstrapper.php');


use Facebook\FacebookSession;
use Facebook\FacebookRequest;

// Gamle København - default group
$groupid = "109602915873899";	
$groupName = "Gamle København";
$group = $_GET["group"];
if($group == "2" ){
	// Set to another group id
	$groupid = "109602915873899";	// TODO: change
	$groupName = "Vesterbro Billeder";
}


$outputFileName = "harvest_output.json";
// nor = number of records
$numberOfRecords = $_GET["nor"];
if($numberOfRecords == ""){
	$numberOfRecords = 10;
}

$saver = new DataSaver();
$saver->init();

// En app som Jacob Andresen har lavet og hentet id / secret til 
FacebookSession::setDefaultApplication('294028730792515', 'ea0876ce78b6f88d33d3cc6976882989');

	// Lav den initielle høstning
	$obj = harvestFeed($numberOfRecords, $groupid);
	
	$dataArr = $obj->getProperty("data")->asArray();	
	$saver->savePosts($dataArr);

	foreach ($dataArr as $d) {
		if(isset($d->picture)){ // Only get commets for posts with pictures
			$comments = harvestComments($d->id);
			$commentsArr = $comments->getProperty("data")->asArray();
			$saver->saveComments($commentsArr, $d->id);
		}
	}

	$res = $saver->getStatistic();

	echo "posts: ".$res['posts']."\n";
	echo "comments: ".$res['comments']."\n";
	echo "keywords: ".$res['keywords']."\n";
	echo "keywords_comments: ".$res['keywords_comments']."\n";


/**
*	Høster fra facebook og returnerer et FB.graphObject
*    
*	// Get Feed id and comments
*   // feed?fields=id,message&limit=100
*
*	@param $limit antallet af poster der skal hentes fra FB 
*	@param $groupid id (ikke navn), der skal hentes et eller andet sted fra
*
*
*/
function harvestFeed( $limit , $groupid) {
$session = FacebookSession::newAppSession();

	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', '/'.$groupid.'/feed?fields=id,message,link,created_time,picture&limit='.$limit);
  		$response = $request->execute();
  		$graphObject = $response->getGraphObject();
  		return $graphObject;


	} catch (FacebookRequestException $ex) {
  		// Session not valid, Graph API returned an exception with the reason.
  		echo $ex->getMessage();
	} catch (\Exception $ex) {
  		// Graph API returned info, but it may mismatch the current app or have expired.
  		echo $ex->getMessage();
	}

}


/**
**
* Høst kommentarer
*
* // Get comments from given thread (Not limited to limit 25)
* // comments?summary=true&limit=1000&order=ranked|chronological
*
* @param $url den url der hentes ud fra FB's paging parameter
*
*/
function harvestComments( $commentId ) {
	$session = FacebookSession::newAppSession();
	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', '/'.$commentId.'/comments?summary=true&limit=1000&order=ranked');
  		$response = $request->execute();
  		$graphObject = $response->getGraphObject();
  		return $graphObject;

	} catch (FacebookRequestException $ex) {
  		// Session not valid, Graph API returned an exception with the reason.
  		echo $ex->getMessage();
	} catch (\Exception $ex) {
  		// Graph API returned info, but it may mismatch the current app or have expired.
  		echo $ex->getMessage();
	}

}

/**
* Save a file to disc
* Use this when not calling the API
* 
*/
function saveAsFile( $fileContent, $fileName ){
	$myfile = fopen($fileName, "w") or die("Unable to open file!");
	
	fwrite($myfile, $fileContent);
	fclose($myfile);
}


?>