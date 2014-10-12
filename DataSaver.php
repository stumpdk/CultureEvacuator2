<?php

	class DataSaver{
		private $ner;
		private $keywords;
        private $numOfPosts = 0;
        private $numOfLikes = 0;
        private $numOfCoordinates = 0;
        private $numOfKeywords = 0;

        public function init(){
			$this->ner = new NamedEntityRecognizer();        	
        }

		public function loadAndParse(){
			/*$myfile = fopen("./testdata_mini.json", "r") or die("Unable to open file!");
			$jsonArray = json_decode(fread($myfile,filesize("./testdata_mini.json")));
			fclose($myfile);
*/
			// $json_data = file_get_contents('./testdata_mini.json');
			// $arrayNow = json_decode($json_data, true);


			$this->ner = new NamedEntityRecognizer();
			$this->keywords = array();

			//$jsonIterator = JSONIterator::getIterator($jsonArray);
            //$arrayNow = json_decode($jsonArray, true);
			//Get ready to put the data in the base!
			foreach ($arrayNow as $key => $val) {
			    //foreach($val as $curPost){
             //       $this->savePosts($val);
                //}
			}

            //var_dump($this->keywords);
            //$coordinatesAndPosts = array();
            //$geoCoder = new GeoCoder();
			/*
            $i = 0;
            foreach($this->keywords as $keyword){
                if(isset($keyword['keywords']['addresses'])){
                    $this->saveKeywords($keyword['keywords']['addresses'], $keyword['post_id'], $keyword['comment_id'],'addresses');
                    foreach($keyword['keywords']['addresses'] as $address){
                        $coordinates = $geoCoder->geocode($address);
                        if($coordinates){
                            $coordinatesAndPosts[] = array($keyword['comment_id'], $coordinates);
                            $i++;
                        }
                    }
                }
                if(isset($keyword['keywords']['tags']))
                	$this->saveKeywords($keyword['keywords']['tags'], $keyword['post_id'],$keyword['comment_id'], 'tags');
                if(isset($keyword['keywords']['names']))
                	$this->saveKeywords($keyword['keywords']['names'], $keyword['post_id'],$keyword['comment_id'], 'names');
                if(isset($keyword['keywords']['years']))
                	$this->saveKeywords($keyword['keywords']['years'], $keyword['post_id'],$keyword['comment_id'], 'years');


            }*/
           // echo 'Number of coordinates: ' . $i;
            //echo json_encode($coordinatesAndPosts);

/*

            echo '<h1>Data saved.</h1>';
            echo '<p>Found:</p>';
            echo '<p>' . $this->numOfPosts . ' poster</p>';
            echo '<p>' . $this->numOfComments . '  kommentarer</p>';
            echo '<p>' . $this->numOfLikes . ' likes</p>';
            echo '<p>' . $i . ' koordinater</p>';*/	
		}


/******* JAC *********/

		/**
		* Iterates over posts and call savePost
		*/
		public function savePosts($data){
			foreach($data as $d){
				if(isset($d->picture)){
					$this->getKeyWords($d->message, $d->id);	
                    $this->savePost($d);
                    echo "Post med id ".$d->id." gemt i databasen \n";
                }
			}
		}

		/**
		* Parse single FB post and puts int in DB
		* Unique constraint on post_id ensures no dobbles
		*/
		function savePost($d){
	        if(isset($d->picture)){ // only save posts with pictures 
                $stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_posts (post_id,picture,link,created_time, updated_time ,message) VALUES (?,?,?,?,?,?)");

                if($stmt){
                    /* Bind our params */
                    $stmt->bind_param('ssssss', $d->id, $d->picture , $d->link, $d->created_time, $d->updated_time, $d->message);
                    $stmt->execute();
                    $this->numOfPosts++;  // TODO Why???
                }
                else{
                    die( 'Statement could not be prepared when saving posts: ' . Database::getInstance()->getError() );
                }
            }
		}

		/**
		* Iterated over comments related to a Post, and caal saveComments
		*/
		function saveComments($comments, $postId){
			foreach($comments as $c){
				if(isset($c->message)){ //Only save comments with a message
					$this->getKeyWords($c->message, $postId , $c->id);	
					$this->saveComment($c, $postId);					
				}
			}
		}

	
		function saveComment($comment, $postId){
	        /* Create the prepared statement */
	        $stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_comments (fb_comment_id, post_id, message,created_time, like_count, user_id, user_name) "
	        . "VALUES (?,?,?,?,?,?,?)");
	        if($stmt){
	            /* Bind our params */
	            $stmt->bind_param('sssssss', $comment->id, $postId, $comment->message, $comment->created_time, $comment->like_count, $comment->from->id, $comment->from->name);

	            $stmt->execute();                
	        }
	        else{
	        	die( 'Statement could not be prepared when saving comments: ' . Database::getInstance()->getError() );
	        }
		}


		function getKeyWords($message, $postId, $commentId = "Post level keyword"){
			// Get the NERs
			$ners = $this->ner->parse($message);
			
			if(isset($ners['tags'] )){
				$this->saveKeywords($ners['tags'], $postId, $commentId, 'tags');
			}
			if(isset($ners['addresses'] )){
				$this->saveKeywords($ners['addresses'], $postId, $commentId, 'addresses');
			}
			if(isset($ners['institutions'] )){
				$this->saveKeywords($ners['institutions'], $postId, $commentId, 'institutions');
			}
			if(isset($ners['years'] )){
				$this->saveKeywords($ners['years'], $postId, $commentId, 'years');
			}
			if(isset($ners['names'] )){
				$this->saveKeywords($ners['names'], $postId, $commentId, 'names');
			}

		}


		/**
		* Iterate over type of Named Enities and calls getKeywordId and saveCommentKeyword
		*
		*/
		function saveKeywords($keywords, $postId, $commentId, $type){
			foreach ($keywords as $keys => $val) {
				$id = $this->getKeywordId($val, $type);
				$this->saveCommentKeyword($id, $commentId, $postId);
			}
		}


		function getKeywordId($keyword, $type){
			$result = Database::getInstance()->runQueryGetAssoc("select id FROM ce_keywords WHERE keyword = '" . $keyword . "' AND type = '" . $type . "'");
			if($result['id'] != null) {
				return $result['id'];
			}
			
			$query = "INSERT INTO ce_keywords (keyword, type) VALUES ('" . $keyword . "', '" . $type . "')";

            Database::getInstance()->executeQuery($query);

			return Database::getInstance()->getInsertId();
		}

		function saveCommentKeyword($keywordId, $commentId, $postId){
			$stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_keywords_comments (keyword_id, comment_id, post_id) VALUES (?,?,?)");
			if($stmt){
				$stmt->bind_param('sss', $keywordId, $commentId, $postId);
	            $stmt->execute();
                $this->numOfKeywords++;
            }
            else{
            	die( 'Statement could not be prepared when saving keywords_comments: ' . Database::getInstance()->getError() );
            }
		}


		/************* END JAC ************/	



		

		






		function saveCoordinates($coordinates, $postId){
			foreach($coordinates as $coordinate){
				$this->saveCoordinate($coordinate, "", $postId);
			}
		}

		function saveCoordinate($coordinate, $address, $postId){
			$stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_coordinates (post_id, address, lng, lat) VALUES (?,?,?)");
			if($stmt){
				$stmt->bind_param('sss', $postId, $address, $coordinate['lng'], $coordinate['lat']);
	            $stmt->execute();
                $this->numOfCoordinates++;
            }
            else{
            	die( 'Statement could not be prepared when saving coordinates: ' . Database::getInstance()->getError() );
            }
		}

		//TODO: Count number of likes and comments pr. user
		function getUserInformations(){

		}
	}
?>
