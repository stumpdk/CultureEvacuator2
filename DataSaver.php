<?php

	class DataSaver{ 

		public function loadAndParse(){
			$myfile = fopen("./testdata.json", "r") or die("Unable to open file!");
			$jsonArray = fread($myfile,filesize("./testdata.json"));
			fclose($myfile);

			$jsonIterator = JSONIterator::getIterator($jsonArray);
            $arrayNow = json_decode($jsonArray, true);
			//Get ready to put the data in the base!
			foreach ($arrayNow as $key => $val) {
			    //foreach($val as $curPost){
                    $this->savePosts($val);
                //}
			}
            
            echo '<h1>Data saved. Maybe...</h1>';
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
				$this->saveComment($c, $postId);
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

		//TODO: Count number of likes and comments pr. user
		function getUserInformations(){

		}
	}
?>