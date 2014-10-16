<?php

	class DataSaver{
		private $ner;
		private $geoCoder;
		private $keywords;
        private $numOfPosts = 0;
        private $numOfLikes = 0;
        private $numOfCoordinates = 0;
        private $numOfKeywords = 0;

        public function init(){
			$this->ner = new NamedEntityRecognizer();        	
			$this->geoCoder = new geoCoder();
        }

        public function testGeo(){
        	$addr = $this->geoCoder->geocode("Lyngbyvej 67");
			if($addr != null ){
				echo print_r($addr);	
			} else {
				echo "ingen geokoordinater";
			}
        }

		/**
		* Iterates over posts and call savePost
		*/
		public function savePosts($data){
			foreach($data as $d){
				if(isset($d->picture)){
					$this->getKeyWords($d->message, $d->id);	
                    $this->savePost($d);
                    return $d->id;                    
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
				// $this->saveKeywords($ners['tags'], $postId, $commentId, 'tags');
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
		* Iterate over a type of Named Enities and calls getKeywordId and saveCommentKeyword
		*
		*/
		function saveKeywords($keywords, $postId, $commentId, $type){
			foreach ($keywords as $keys => $val) {
				$id = $this->getKeywordId($val, $type);
				$this->saveCommentKeyword($id, $commentId, $postId);
				if($type === "addresses"){
					/* Dette er slået fra, da den nærmest aldring rammer noget
					   og tager lang tid....
					   Og i øvrigt ikke er implmenteret i DB
						TODO: Brug Google geocoder
					$addr = $this->geoCoder->geocode($val);	
					if($addr != null ){
						saveCoordinate($addr, $val, $postId);	
						echo "gemmer (ikke) geocoordinat for ".$val."\n";
					} else {
						echo "ingen geokoordinater til ".$val."\n";
					}
					*/					
				}
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


		public function getStatistic(){
			$posts = Database::getInstance()->runQueryGetAssoc("select count(*) as count FROM ce_posts");
			$comments = Database::getInstance()->runQueryGetAssoc("select count(*) as count FROM ce_comments");
			$keywords = Database::getInstance()->runQueryGetAssoc("select count(*) as count FROM ce_keywords");
			$keywords_comments = Database::getInstance()->runQueryGetAssoc("select count(*) as count FROM ce_keywords_comments");
			
			// $res = new Array();
			$res['posts'] = $posts['count'];
			$res['comments'] = $comments['count'];
			$res['keywords'] = $keywords['count'];
			$res['keywords_comments'] = $keywords_comments['count'];
			return $res;
		}

		/**
		*
		* VIRKER IKKE ENDNU 
		* Tabel ikke implementeret
		*
		*
		*/
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

		
	}
?>
