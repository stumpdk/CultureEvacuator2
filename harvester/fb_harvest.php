<?php

/**
* fb_harvest.php?nor=10&group=1
* nor = NumberOfRecords to harvest from facebook. Default is 10
* group: 
*   1 = Gamle København
*   2 = Vesterbro Billeder 
*/

header('Content-Type: text/html');
include_once('./autoload.php');
include_once('./Bootstrapper.php');


use Facebook\FacebookSession;
use Facebook\FacebookRequest;


	include_once('./includes/header.php');
	
	?>

	<section>
      <div class="container">
        <div class="row">
        <form> 
        <h3>Vælg hvad der skal høstes</h3>
        <p>Vi høster kun poster med et billed tilknyttet, samt kommentarer til disse.</p>
          <div class="col-md-4">
          	<h5>Vælg gruppe:</h5>
          	   <div class="radio">
  					<label>
    					<input type="radio" name="group" id="group" value="1" checked>
    					Gamle København
  					</label>
				</div>
				<div class="radio disabled">
  					<label>
    					<input type="radio" name="group" id="group" value="2">
    					Gruppe 2
  					</label>
				</div>
				<div class="radio disabled">
  					<label>
    					<input type="radio" name="group" id="group" value="2">
    					Gruppe 3
  					</label>
				</div>				
          </div>

          <div class="col-md-4">
          <h5>Vælg antal posts:</h5>
          	<select name="nor" class="form-control">
  				<option>1</option>
  				<option>5</option>
  				<option>10</option>
  				<option>50</option>
		  	</select>
		 </div>

		 <div class="col-md-4">
		 <h5>Go:</h5>
		 	 <button type="submit" class="btn btn-default">Høst</button>

		 </div>

		 </form>
        </div>
      </div>
	<section>	


	


<?php


	
	
	
	if( isset($_GET["group"]) && isset($_GET["nor"]) ){




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

?>
	<section>
    	<div class="container">
        	<div class="row">
        		<div class="col-md-6">	
        		<h5>Der er nu høstet, her er lidt statistik fra basen:</h5>
  					<table class="table table-striped">
  					<tbody>	
  					
  						
  					
<?php
	echo "<tr><td>Antal Poster: </td><td>".$res['posts']."</td></tr>";
	echo "<tr><td>Antal Kommentarer: </td><td>".$res['comments']."</td></tr>";
	echo "<tr><td>Antal Unikke NER's: </td><td>".$res['keywords']."</td></tr>";
	echo "<tr><td>Antal NER's tilknyttet poster eller kommentarer: </td><td>".$res['keywords_comments']."</td></tr>";

?>
					</tr>
  					</tbody>
				</table>
				</div>
			</div>
		</div>
	</section>

<?php
} // End if-else
?>

	<section>
		<div class="container">
        	<div class="row">
        		<div class="col-md-12">	
        		<h4>Brug json API'et</h4>
        		<p>
        			<strong>Hent alle poster:</strong><br/>
        			<a target="_blank" href="../api/public/1/?type=posts&callback=?">../api/public/1/?type=posts&callback=?</a><br/>
        			<br/>

					<strong>Hent alle post med id 109602915873899_370938319740356 (internt fb id):</strong><br/>
        			<a target="_blank" href="../api/public/1/?type=posts&post_id=109602915873899_370938319740356&callback=?">../api/public/1/?type=posts&post_id=109602915873899_370938319740356&callback=?</a><br/>
        			<br/>

        			<strong>Hent alle kommentarer til post med id 109602915873899_370938319740356 (internt fb id):</strong><br/>
        			<a target="_blank" href="../api/public/1/?type=comments&post_id=109602915873899_370938319740356&callback=?">../api/public/1/?type=comments&post_id=109602915873899_370938319740356&callback=?</a><br/>
        		</p>
        		</div>
    		</div>
    	</div>
	</section>

<?php
include_once('./includes/footer.php');

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