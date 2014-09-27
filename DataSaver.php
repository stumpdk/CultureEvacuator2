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
		}

		function savePosts($data){
			foreach($data as $d){
                if(!isset($d['paging'])){
                    $this->savePost($d);
                    if(isset($d['comments']))
                    	$this->saveComments($d['comments']['data'], $d['id']);
                    $this->saveLikes($d['likes']['data'], $d['id']);
                }
			}
		}

		function savePost($d){
			$sql = 'INSERT INTO posts (\'picture\',\'link\',\'created_time\', \'message\') VALUES (' 
                    . $d['message'] . ', ' 
                    . $d['picture'] . ', '
                    . $d['link'] . ', ' 
                    . $d['created_time'] . ')';
			Database::getInstance()->executeQuery($sql);
		}

		function saveComments($comments, $postId){
			foreach($comments as $c){
				$this->saveComment($c, $postId);
			}
		}

		function saveComment($comment, $postId){
			//$sql = "INSERT INTO comments ('fb_id', 'post_id', 'message','created_time', 'like_count', 'user_id', 'user_name') VALUES ($comment['id'], $postId, $comment['message'], $comment['created_time'], $comment['like_count'], $comment['from']['id'], $comment['from']['name'])";
			
          //  $db->query($sql);	
        /* Create the prepared statement */
        $stmt = Database::getInstance()->prepare("INSERT INTO comments ('fb_id', 'post_id', 'message','created_time', 'like_count', 'user_id', 'user_name') "
        . "VALUES (?,?,?,?,?,?,?)");

            /* Bind our params */
            $stmt->bind_param('isssds', $comment['id'], $postId, $comment['message'], $comment['created_time'], $comment['like_count'], $comment['from']['id'], $comment['from']['name']);

            $stmt->execute();
		}

		function saveLikes($likes, $postId){
			foreach($likes as $l){
				$this->saveLike($l, $postId);
			}
		}

		function saveLike($like, $postId){
			$stmt = Database::getInstance()->prepare("INSERT INTO likes ('fb_id', 'post_id', 'user_id') VALUES (?,?,?)");
			$stmt->bind_param('iii', $like['id'], $postId, $like['id']);
            $stmt->execute();	
		}

		//TODO: Count number of likes and comments pr. user
		function getUserInformations(){

		}
	}
?>