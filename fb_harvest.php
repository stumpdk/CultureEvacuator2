<?php
header('Content-Type: application/json');
include_once('./autoload.php');


use Facebook\FacebookSession;
use Facebook\FacebookRequest;


// Gamle København 
// Skal parametiseres vha
$groupid = "109602915873899";

// En app som Jacob Andresen har lavet og hentet id / secret til 
FacebookSession::setDefaultApplication('294028730792515', 'ea0876ce78b6f88d33d3cc6976882989');

	// Lav den initielle høstning
	$obj = harvestFirst(10, $groupid);
	
	// Lab array for at kunne måle størrelsen
	$dataArr = $obj->getProperty("data")->asArray();
	
	// "data er ...... data :-)"
	$data = $obj->getProperty("data");

	// Skriv til skærm 
	echo print_r($data);
	// TODO: Kald insert_into_mysql_database()

	$arrSize = sizeof( $dataArr, 1);

  	// Iterer over posts, for at kunne finde kommentarer
  	for ($i = 0; $i < $arrSize; $i++){

  			// Hent "paging next" URL step-by-step
			$content = 	$data->getProperty($i);
			$comments = $content->getProperty("comments");
			
			$paging = $comments->getProperty("paging");
			$next = $paging->getProperty("next");
			
			// Dette kan vi bruge til at hente flere kommentarer
			if($next){
				
				$morecomments = harvestOneMore( $next );	
				// Kald "flere kommentarer service"
				
			}
  	 	$i++;
  	}

  
  	



/**
*	Høster fra facebook og returnerer et FB.graphObject
*	@param $limit antallet af poster der skal hentes fra FB 
*	@param $groupid id (ikke navn), der skal hentes et eller andet sted fra
*
*
*/
function harvestFirst( $limit , $groupid) {
$session = FacebookSession::newAppSession();

	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', '/'.$groupid.'/feed?limit='.$limit);
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
* Høst kommentarer
* @param $url den url der hentes ud fra FB's paging parameter
*
*/
function harvestOneMore( $url ) {
	$session = FacebookSession::newAppSession();

	// Arghhh  - der skal skærlles de første 31 tegn af 
	// Før det bliver brugbart for FacebookRequest
	// http://.... v2.1/ (skrælles væk)
	$nexturl = substr($url, 31);
	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', $nexturl);
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


?>