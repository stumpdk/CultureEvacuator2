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


// En app som Jacob Andresen har lavet og hentet id / secret til 
FacebookSession::setDefaultApplication('294028730792515', 'ea0876ce78b6f88d33d3cc6976882989');

	// Lav den initielle høstning
	$obj = harvestFeed($numberOfRecords, $groupid);
	
	// Lab array for at kunne måle størrelsen
	$dataArr = $obj->getProperty("data")->asArray();
	$objArr = $obj->asArray();
	
	// "data er ...... data :-)"
	$data = $obj->getProperty("data");

	// Save content in file	
	saveAsFile(json_encode($dataArr), $outputFileName);
	 
	 echo $numberOfRecords." poster høstet fra gruppen ". $groupName . ", og gemt i filen '" . $outputFileName."'\n";
	// TODO: Kald insert_into_mysql_database()

	 $i = 0;
	foreach ($dataArr as $v1) {
		$comments = harvestComments($v1->id);
		$commentsArr = $comments->getProperty("data")->asArray();
		saveAsFile(json_encode($commentsArr), 'comments_'.$i.'_'.$outputFileName);
		$i++;

	}

	//Call next page



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

  		$request = new FacebookRequest($session, 'GET', '/'.$groupid.'/feed?fields=id,message,link&limit='.$limit);
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