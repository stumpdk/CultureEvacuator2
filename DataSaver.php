<?php

	include_once('./config.php');

	public class DataSaver{ 

		public function loadAndParse(){
			$myfile = fopen("testdata.json", "r") or die("Unable to open file!");
			$jsonArray = fread($myfile,filesize("testdata.json"));
			fclose($myfile);

			$jsonIterator = JSONIterator::getIterator($jsonArray);

			//Get ready to put the data in the base!
			foreach ($jsonIterator['data'] as $key => $val) {
			    $this->savePosts($val);
			    /*if(is_array($val)) {
			        echo "$key:\n";
			    } else {
			        echo "$key => $val\n";
			    }*/

			}
		}

		function savePosts($data){
			foreach($data as $d){
				$this->savePost($d);
				$this->saveComments($d['comments']['data'], $d['id']);
				$this->saveLikes($d['likes']['data'], $d['id']);
			}
		}

		function savePost($post){
			$sql = "INSERT INTO posts ('picture','link','created_time', 'message') 
					VALUES ($d['message'], $d['picture'],$d['link'],$d['created_time'])";
			$db->query($sql);
		}

		function saveComments($comments, $postId){
			foreach($comments as $c){
				$this->saveComment($c, $postId);
			}
		}

		function saveComment($comment, $postId){
			$sql = "INSERT INTO comments ('fb_id', 'post_id', 'message','created_time', 'like_count', 'user_id', 'user_name') 
					VALUES ($comment['id'], $postId, $comment['message'], $comment['created_time'], $comment['like_count'], $comment['from']['id'], $comment['from']['name'])";
			$db->query($sql);		
		}

		function saveLikes($likes, $postId){
			foreach($likes as $l){
				$this->saveLike($l, $postId);
			}
		}

		function saveLike($like, $postId){
			$sql = "INSERT INTO likes ('id', 'post_id', 'user_id') 
					VALUES ($like['id'], $postId, $like['id'])";
			$db->query($sql);	
		}

		//TODO: Count number of likes and comments pr. user
		function getUserInformations(){

		}
	}
?>