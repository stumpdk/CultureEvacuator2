<?php

	class DataSaver{ 
		private $ner;  
		private $keywords;

		public function loadAndParse(){
			$myfile = fopen("./testdata.json", "r") or die("Unable to open file!");
			$jsonArray = fread($myfile,filesize("./testdata.json"));
			fclose($myfile);

			$this->ner = new NamedEntityRecognizer();
			$this->keywords = array();

			//$jsonIterator = JSONIterator::getIterator($jsonArray);
            $arrayNow = json_decode($jsonArray, true);
			//Get ready to put the data in the base!
			foreach ($arrayNow as $key => $val) {
			    //foreach($val as $curPost){
                    $this->savePosts($val);
                //}
			}

            //var_dump($this->keywords);
            $coordinatesAndPosts = array();
            $geoCoder = new GeoCoder();
            
            foreach($this->keywords as $keyword){
                if(isset($keyword['addresses'])){
                    foreach($keyword['addresses'] as $address){                        
                        $coordinatesAndPosts[] = array($keyword['comment_id'], $geoCoder->geocode($address));
                    }
                }
            }
            echo json_encode($coordinatesAndPosts);

            //echo json_encode($this->keywords);

            //echo '<h1>Data saved. Maybe...</h1>';
		}

		function savePosts($data){
			foreach($data as $d){
                if(!isset($d['paging'])){
                    $this->savePost($d);
                    if(isset($d['comments'])){

                    	$this->saveComments($d['comments']['data'], $d['id']);
                    }
                    if(isset($d['likes'])){
                    	$this->saveLikes($d['likes']['data'], $d['id']);
                    }
                }
			}
		}

		function savePost($d){
	        if(isset($d['message'])){
                $stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_posts (picture,link,created_time, message) VALUES (?,?,?,?)");

                if($stmt){
                    /* Bind our params */

                    $stmt->bind_param('ssss', $d['message'] , $d['picture'], $d['link'] , $d['created_time']);

                    $stmt->execute();
                }
                else{
                    die( 'Statement could not be prepared when saving posts: ' . Database::getInstance()->getError() ); 
                }
            }
		}

		function saveComments($comments, $postId){
			foreach($comments as $c){
				if(isset($c['message'])){
					$keysAndId['comment_id'] = $c['id'];
					$keysAndId['keywords'] = $this->ner->parse($c['message']);
					$this->keywords[] = $keysAndId;

					$this->saveComment($c, $postId);
				}
			}
		}

		function saveComment($comment, $postId){
	        /* Create the prepared statement */
	        $stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_comments (fb_id, post_id, message,created_time, like_count, user_id, user_name) "
	        . "VALUES (?,?,?,?,?,?,?)");
	        if($stmt){
	            /* Bind our params */
	            $stmt->bind_param('sssssss', $comment['id'], $postId, $comment['message'], $comment['created_time'], $comment['like_count'], $comment['from']['id'], $comment['from']['name']);

	            $stmt->execute();
	        }
	        else{
	        	die( 'Statement could not be prepared when saving comments: ' . Database::getInstance()->getError() ); 
	        }
		}

		function saveLikes($likes, $postId){
			foreach($likes as $l){
				$this->saveLike($l, $postId);
			}
		}

		function saveLike($like, $postId){
			$stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_likes (fb_id, post_id, user_id) VALUES (?,?,?)");
			if($stmt){
				$stmt->bind_param('sss', $like['id'], $postId, $like['user_id']);
	            $stmt->execute();
            }
            else{
            	die( 'Statement could not be prepared when saving likes: ' . Database::getInstance()->getError() ); 
            }
		}

		function saveKeywords($keywords, $postId, $commentId){
			foreach($keywords as $keyword){
				$id = $this->getKeywordId($keyword);
				$this->saveCommentKeyword($keywordId, $postId, $commentId);
			}
		}

		function getKeywordId($keyword){
			$result = Database::getInstance()->query("select id FROM ce_keywords WHERE keyword LIKE ?");
			
			if($result['id'])
				return $result['id'];

			Database::getInstance()->query("INSERT INTO ce_keywords (keyword) VALUES (" . $keyword . ")");

			return Database::getInstance()->getInsertId();	
		}

		function saveCommentKeyword($keywordId, $commentId, $postId){
			$stmt = Database::getInstance()->prepareStatement("INSERT INTO ce_keywords_comments (keyword_id, comment_id, post_id) VALUES (?,?,?)");
			if($stmt){
				$stmt->bind_param('iii', $keywordId, $commentId, $postId);
	            $stmt->execute();
            }
            else{
            	die( 'Statement could not be prepared when saving addresses: ' . Database::getInstance()->getError() ); 
            }
		}

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
            }
            else{
            	die( 'Statement could not be prepared when saving addresses: ' . Database::getInstance()->getError() ); 
            }
		}

		//TODO: Count number of likes and comments pr. user
		function getUserInformations(){

		}
	}
?>