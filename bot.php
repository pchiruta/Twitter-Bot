<?php
require '/oauth/twitteroauth.php';
require '/config.php';
class TwitterAutoResponder {
	
	private $_replies = array();
	private $_connection;
	public function __construct() {
		$this->_connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	}
	
	public function addReplies($term, $replyarray)
	{
	                // Find the total number of rows in the array
	                $replycount = count($replyarray);
	                // Offset for array's zero val
	                $replycount--;
	                
	                // Generate a random number as key for the array
	                $rand = rand(0,$replycount);
	                
	                // Plug in the key & add that reply!
	                $this->_replies[$term] = $replyarray[$rand];            
	}
	
	public function run($term,$user,$response,$retweet,$follow) {
		echo '========= '.date('Y-m-d g:i:s A')." - Started =========\n";
		
		echo "given args are : term :".$term." , user :".$user." , response :".$response." , retweet :".$retweet." , follow:".$follow."\n";
		// Get the last ID we replied to
		$since_id = @file_get_contents('since_id');
		if ($since_id == null) {
			$since_id = 0;
		}
		// Store the ID of the last tweet
		$max_id = $since_id;
		// Verify the Twitter account exists
		if (!$this->verify()) {
			$this->_auth();
			die();
		}
		
		if(isset($term) && $term != ""){
			echo 'Performing search for '.$term.'... ';
			$search = $this->search($term, $since_id);
			//Print_r($search);
			//$search = $this->_connection->get('https://api.twitter.com/1.1/search/tweets.json?');
			echo 'Done, '.count($search->statuses).' results';
			echo "\n";
			// Store the max ID
			
			if ($search->search_metadata->max_id_str > $max_id) {
				$max_id = $search->search_metadata->max_id_str;
			}

			// Loop through the results and reply back
			if(isset($response) && $response != ""){
				echo "Inside reply-to block..".'\n';
				foreach ($search->statuses as $tweet) {
					$this->reply($tweet, $response);
				}
			}
			
			if(isset($retweet) && $retweet == "true"){
				echo "Inside retweet block..".'\n';
				foreach ($search->statuses as $tweet) {
					$this->retweet($tweet->id_str);
				}
			}
			
			if(isset($follow) && $follow == "true"){
				echo "Inside follow block..".'\n';
				foreach ($search->statuses as $tweet) {
					$this->follow($tweet->user->id);
				}
			}
		}
		
		if(isset($user) && $user != ""){
			echo 'Performing search for '.$user.'... ';
			$search = $this->search_user($user);
			//Print_r($search);
			//$search = $this->_connection->get('https://api.twitter.com/1.1/search/tweets.json?');
			//Print_r($search);
			//file_put_contents("response.txt",$search,FILE_APPEND);
			$count = count($search);
			echo 'Done, '.$count.' results';
			echo "\n";
			
			
			for($x = 0; $x < $count; $x++) {
				// Store the max ID
				if ($search[$x]->id_str > $max_id) {
				$max_id = $search[$x]->id_str;
				}
				
				if(isset($response) && $response != ""){
					echo "Inside reply-to block..".'\n';	
					$this->reply_user($search[$x]->user->screen_name,$search[$x]->id_str,$response,$search[$x]->text);
				}
			
				if(isset($retweet) && $retweet == "true"){
					echo "Inside retweet block..".'\n';
					$this->retweet($search[$x]->id_str);
				}
			}
			
			if(isset($follow) && $follow == "true"){
					echo "Inside follow block..".'\n';
					$this->follow($search[0]->user->id_str);
			}
			
		}
		
		
		file_put_contents('since_id', $max_id);
		echo '========= '.date('Y-m-d g:i:s A')." - Finished =========\n";
	}
	private function reply($tweet, $reply) {
		try {
			echo '@'.$tweet->user->screen_name.' said: '.$tweet->text."\n";
			$res = $this->_connection->post('statuses/update', array(
				'status' => '@'.$tweet->user->screen_name.' '.$reply,
				'in_reply_to_status_id' => $tweet->id_str,
			));
			//Print_r($res);
		}
		catch (OAuthException $e) {
				echo 'ERROR: '.$e->message;
				die();
		}
	}
	
	private function reply_user($username,$id_str,$reply, $text) {
		try {
			echo '@'.$username.' said: '.$text."\n";
			$res = $this->_connection->post('statuses/update', array(
				'status' => '@'.$username.' '.$reply,
				'in_reply_to_status_id' => $id_str,
			));
		}
		catch (OAuthException $e) {
				echo 'ERROR: '.$e->message;
				die();
		}
	}
	private function verify() {
		try {
			$this->_connection->get('account/verify_credentials');
			return true;
		} catch (OAuthException $e) {
			return false;
		}
	}
	private function _auth() {
		// First get a request token, then prompt them to go to the URL
		echo 'OAuth Verification Needed. Retrieving request token...';
		$request_token = $this->_connection->getRequestToken();
		$redirect_url = $this->_connection->getAuthorizeUrl($request_token);
		echo 'Please navigate to this URL for authentication: '.$redirect_url;
		echo 'Once done, and you have a PIN Number, press ENTER.';
		fread(STDIN, 10);
		echo 'PIN Number: ';
		$pin = trim(fread(STDIN, 10));
		// Swap the PIN for an access token
		//!!!
	}
	
	private function search($term, $since_id){
		$query = array("q"=>$term,"since_id"=>$since_id,"lang"=>"en","result_type"=>"recent","count"=>5);
		return $this->_connection->get('search/tweets',$query);
	}
	
	public function search_user($user){
		$query = array("screen_name"=>$user);
		return $this->_connection->get('statuses/user_timeline',$query);
	}
	
	
	/*public function run_retweet($term) {
		echo '========= '.date('Y-m-d g:i:s A')." - Started =========\n";
		// Get the last ID we replied to
		$since_id = @file_get_contents('since_id');
		if ($since_id == null) {
			$since_id = 0;
		}
		// Store the ID of the last tweet
		$max_id = $since_id;
		// Verify the Twitter account exists
		if (!$this->verify()) {
			$this->_auth();
			die();
		}
		// Loop through the replies
		//foreach ($this->_replies as $term => $reply) {
			echo 'Performing search for '.$term.'... ';
			$search = $this->search($term, $since_id);
			//$search = $this->_connection->get('https://api.twitter.com/1.1/search/tweets.json?');
			echo 'Done, '.count($search->statuses).' results';
			echo "\n";
			// Store the max ID
			
			if ($search->search_metadata->max_id_str > $max_id) {
				$max_id = $search->search_metadata->max_id_str;
			}
			// Loop through the results
			foreach ($search->statuses as $tweet) {
				echo "id_str is :".$tweet->id_str.'\n';
				$this->retweet($tweet->id_str);
			}
		//}
		file_put_contents('since_id', $max_id);
		echo '========= '.date('Y-m-d g:i:s A')." - Finished =========\n";
	}*/
	
	public function retweet($tweet_id){
		try {
			$this->_connection->post('statuses/retweet/'.$tweet_id);
		}
		catch (OAuthException $e) {
				echo 'ERROR: '.$e->message;
				die();
		}
	}
	
	private function follow($id)
	{
		try{
			$res = $this->_connection->post('friendships/create', array('user_id' => $id));
			//Print_r($res);
		}
		catch(OAuthException $e){
			echo 'ERROR: '.$e->message;
				die();
		}
		echo "user with $id is followed"."\n";
	}
	
	public function test($response) {
		$this->_connection->post('statuses/update', array('status' => $response));
	}
}
?>
