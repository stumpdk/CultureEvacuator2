<?php
header('Content-Type: application/json');
include_once('./autoload.php');


use Facebook\FacebookSession;
use Facebook\FacebookRequest;


#294028730792515
#ea0876ce78b6f88d33d3cc6976882989

FacebookSession::setDefaultApplication('294028730792515', 'ea0876ce78b6f88d33d3cc6976882989');

//$access_token = 'CAACEdEose0cBAHnblj3iITmm8RepjJBuxlNEh3SW5ySteByCIkgoOisqhflGwZBGEHSWZAhgBnWUzZBjJHYeymkCrXFhXFZB9RTG4gjdqZBaITj2OFfbMiGLBAVQXS5871ZAOMYnZCe9CeNZC3GKZB0pLnZCUueNDCsoAZA1qYdVgpjzMZCld4RvA5ZAhoUqQ5TDtMQIL3IJKwNGqZCgZDZD';

// If you already have a valid access token:
// $session = new FacebookSession($access_token);
// If you're making app-level requests:


	$obj = harvestFirst();
	
	$dataArr = $obj->getProperty("data")->asArray();
	$data = $obj->getProperty("data");

	$arrSize = sizeof( $dataArr, 1);
  	for ($i = 0; $i < $arrSize; $i++){

			$content = 	$data->getProperty($i);
			$comments = $content->getProperty("comments");
			echo print_r($comments);
			$paging = $comments->getProperty("paging");
			$next = $paging->getProperty("next");
			if($next){
				
				$moremessages = harvestOneMore( $next );	
				
			}

			

  	 	$i++;
  	}

  
  	




function harvestFirst() {
$session = FacebookSession::newAppSession();

	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', '/109602915873899/feed');
  		$response = $request->execute();
  		$graphObject = $response->getGraphObject();
  		return $graphObject;


	} catch (FacebookRequestException $ex) {
  		// Session not valid, Graph API returned an exception with the reason.
  		echo "123";
  		echo $ex->getMessage();
	} catch (\Exception $ex) {
  		// Graph API returned info, but it may mismatch the current app or have expired.
  		echo "12345";
  		echo $ex->getMessage();
	}

}


function harvestOneMore( $url ) {
	echo "\nONE MORE\n";
	$session = FacebookSession::newAppSession();
	$nexturl = substr($url, 31);
	echo $nexturl;
	try {
  		$session->validate();

  		$request = new FacebookRequest($session, 'GET', $nexturl);
  		$response = $request->execute();
  		$graphObject = $response->getGraphObject();

  		echo "\nxxxxxx\n";
		echo print_r( $graphObject->getProperty("data") );
		echo "\nxxxxxx\n";


  		return $graphObject;

	} catch (FacebookRequestException $ex) {
  	// Session not valid, Graph API returned an exception with the reason.
  	echo "123";
  	echo $ex->getMessage();
	} catch (\Exception $ex) {
  	// Graph API returned info, but it may mismatch the current app or have expired.
  	echo "12345";
  	echo $ex->getMessage();
	}

}


?>