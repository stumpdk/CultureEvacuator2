<?php

	class DataSaver{
		private $ner;
		private $keywords;
        private $numOfPosts = 0;
        private $numOfComments = 0;
        private $numOfLikes = 0;
        private $numOfCoordinates = 0;

		public function loadAndParse(){
			/*$myfile = fopen("./testdata_mini.json", "r") or die("Unable to open file!");
			$jsonArray = json_decode(fread($myfile,filesize("./testdata_mini.json")));
			fclose($myfile);
*/
			$json_data = file_get_contents('./testdata.json');
			$arrayNow = json_decode($json_data, true);


			$this->ner = new NamedEntityRecognizer();
			$this->keywords = array();

			//$jsonIterator = JSONIterator::getIterator($jsonArray);
            //$arrayNow = json_decode($jsonArray, true);
			//Get ready to put the data in the base!
			foreach ($arrayNow as $key => $val) {
			    //foreach($val as $curPost){
                    $this->savePosts($val);
                //}
			}

            //var_dump($this->keywords);
            $coordinatesAndPosts = array();
            $geoCoder = new GeoCoder();

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


            }
           // echo 'Number of coordinates: ' . $i;
            //echo json_encode($coordinatesAndPosts);



            echo '<h1>Data saved.</h1>';
            echo '<p>Found:</p>';
            echo '<p>' . $this->numOfPosts . ' poster</p>';
            echo '<p>' . $this->numOfComments . '  kommentarer</p>';
            echo '<p>' . $this->numOfLikes . ' likes</p>';
            echo '<p>' . $i . ' koordinater</p>';
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
                    $this->numOfPosts++;
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
					$keysAndId['post_id'] = $postId;
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
                $this->numOfComments++;
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

		function saveKeywords($keywords, $postId, $commentId, $type){
			if(isset($keywords)){
				foreach($keywords as $keyword){
					$id = $this->getKeywordId($keyword, $type);
					$this->saveCommentKeyword($id, $postId, $commentId);
				}
			}
		}

		function getKeywordId($keyword, $type){
			$result = Database::getInstance()->executeQuery("select id FROM ce_keywords WHERE keyword LIKE '" . $keyword . "' AND type LIKE '" . $type . "'");

			if($result['id'] !== null)
				return $result['id'];
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
