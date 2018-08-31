<?php 

	session_start();

	$link = mysqli_connect("localhost", "root", "password", "twitter");

	if (mysqli_connect_errno()) {

		print_r(mysqli_connect_error());
		exit();

	}

	if ($_GET['function'] == "logout") {

		session_unset();

	}

	function test($data) {

		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	function time_since($since) {
	    $chunks = array(
	        array(60 * 60 * 24 * 365 , 'year'),
	        array(60 * 60 * 24 * 30 , 'month'),
	        array(60 * 60 * 24 * 7, 'week'),
	        array(60 * 60 * 24 , 'day'),
	        array(60 * 60 , 'hour'),
	        array(60 , 'min'),
	        array(1 , 'sec')
	    );

	    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
	        $seconds = $chunks[$i][0];
	        $name = $chunks[$i][1];
	        if (($count = floor($since / $seconds)) != 0) {
	            break;
	        }
	    }

	    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
	    return $print;
	}


	function displayTweets ($type) {

		global $link;

		if ($type == "public") {

			$whereClause = "";

		} else if ($type == "isFollowing") {

			$query = "SELECT * FROM isfollowing WHERE follower = ". mysqli_real_escape_string($link, $_SESSION['id']);
			$result = mysqli_query($link, $query);

			$whereClause = "";

			if (mysqli_num_rows($result) > 0) {

				while($row = mysqli_fetch_assoc($result)) {

					if ($whereClause == "") $whereClause = "WHERE ";
					else $whereClause.= " OR ";
					$whereClause.= "userid = ".$row['isFollowing'];

				}
				$whereClause.= " OR userid = ".$_SESSION['id'];

			} else {

				$query = "SELECT * FROM tweets WHERE userid = ".$_SESSION['id']." LIMIT 1";
				$result = mysqli_query($link, $query);

				if (mysqli_num_rows($result) > 0) {

					$whereClause.= " WHERE userid = ".$_SESSION['id'];

				} else {

					$whereClause = " WHERE `userid` = -1 ";

				}

			}

		} else if ($type == "yourtweets") {

			$whereClause = " WHERE userid = ".mysqli_real_escape_string($link, $_SESSION['id']);

		} else if ($type == "search") {

			echo "<p class='text-center'>Showing results for ".mysqli_real_escape_string($link, $_GET['q'])."</p>";

			$whereClause = " WHERE tweet LIKE '%".mysqli_real_escape_string($link, $_GET['q'])."%'";

		} else if (is_numeric($type)) {

			// echo "<p>Showing results for ".mysqli_real_escape_string($link, $_GET['q'])."</p>";

			$userQuery = "SELECT * FROM users WHERE id =".mysqli_real_escape_string($link, $type)." LIMIT 1";

			$userQueryResult = mysqli_query($link, $userQuery);

			$user = mysqli_fetch_assoc($userQueryResult);

			$whereClause = " WHERE userid = ".mysqli_real_escape_string($link, $type);

		}

		$query = "SELECT * FROM tweets ".$whereClause." ORDER BY datetime DESC LIMIT 10";

		$result = mysqli_query($link, $query);

		if (mysqli_num_rows($result) == 0) {

			if ($type == "isFollowing") {

				echo "There are no tweets to display! Try following some friends!";

			} else {

				echo "There are no tweets to display!";

			}

		} else {

			while ($row = mysqli_fetch_assoc($result)) {

				$tweetId = $row['id'];

				$userQuery = "SELECT * FROM users WHERE id =".mysqli_real_escape_string($link, $row['userid'])." LIMIT 1";

				$userQueryResult = mysqli_query($link, $userQuery);

				$user = mysqli_fetch_assoc($userQueryResult);

				$userId = $row['userid'];

				if (empty($user['photo'])) {

					$photo = "./images/default.JPG";

				} else {

					$photo = $user['photo'];

				}

				if (isset($_SESSION['id'])) {

					if ($userId == $_SESSION['id']) {

						echo "<div class='tweet'>
						<p class='myTweet'>
							<a href='?page=myprofile'><img width='50px' height='50px' src='".$photo."' alt='default user image'></a>
							<a class='' href='?page=myprofile'>".$user['username']."</a>
							<span class='time'>".time_since(time() - strtotime($row['datetime']))." ago</span>
							<a data-tweetId='".$tweetId."'' class='deleteTweet'>Delete</a>
						</p>";

					} else {

						echo "<div class='tweet'>
						<p>
							<a href='?page=publicprofiles&userid=".$user['id']."'><img width='50px' height='50px' src='".$photo."' alt='default user image'></a>
							<a href='?page=publicprofiles&userid=".$user['id']."'>".$user['username']."</a>
							<span class = 'time'>".time_since(time() - strtotime($row['datetime']))." ago</span>
						</p>";

					}

					echo "<p>".$row['tweet']."</p>";

					echo "<div class='numbers'>";

						//get amount of likes
						$likes = "SELECT COUNT(*) AS `number` FROM likes WHERE tweetLiked = ".$tweetId;
						$likesResults = mysqli_query($link, $likes);

						$likeNumber = mysqli_fetch_assoc($likesResults);

						echo "<a class='tweetLikes' data-tweetId='".$tweetId."'>Likes: ".$likeNumber['number']."</a>";


						//get amount of comments
						$comments = "SELECT COUNT(*) AS `number` FROM comments WHERE tweet = ".$tweetId;
						$commentsResults = mysqli_query($link, $comments);

						$commentNumber = mysqli_fetch_assoc($commentsResults);

						echo "<a class='tweetComments' data-tweetId='".$tweetId."'>Comments: ".$commentNumber['number']."</a>";


					echo "</div>"; // end of div numbers

					echo "<div class='options'>";

						//check if this image is liked by this user
						$isLiked = "SELECT * FROM likes WHERE userLiking =".mysqli_real_escape_string($link, $_SESSION['id'])." AND tweetLiked = ".$tweetId;
						$isLikedResults = mysqli_query($link, $isLiked);

						if (mysqli_num_rows($isLikedResults) > 0) {

							echo "<a class='likeTweet' data-tweetId='".$tweetId."'><img id='".$tweetId.$tweetId."' src='./images/liked.png' alt='liked image'></a>";

						} else {

							echo "<a class='likeTweet' data-tweetId='".$tweetId."'><img id='".$tweetId.$tweetId."' src='./images/unliked.png' alt='unliked image'></a>";

						}

						echo "<a class='comment' href='?page=comments&tweet=".$tweetId."' data-tweetId='".$tweetId."'><img id='commentImage".$tweetId."' src='./images/quote.png' alt='quote image'></a>";

					echo "</div>"; // end of div controls

				} else {

					echo "<div class='tweet'>
						<p>
							<a href=''><img width='50px' class='needLogin' height='50px' src='".$photo."' alt='default user image'></a>
							<a class='needLogin' href=''>".$user['username']."</a>
							<span class = 'time'>".time_since(time() - strtotime($row['datetime']))." ago</span>
						</p>";

					echo "<p>".$row['tweet']."</p>";

					echo "

					<p><a class='needLogin btn btn-primary' data-userId='".$userId."'>Follow</a></p>

					<a class='needLogin' data-tweetId='".$tweetId."'><img id='".$tweetId.$tweetId."' src='./images/unliked.png' alt='unliked image'></a>

					<a class='needLogin' data-tweetId='".$tweetId."'><img id='commentImage".$tweetId."' src='./images/quote.png' alt='quote image'></a>";

				}


				echo "</div>"; // end of tweet

			}

		}

	}

	function displayComments($tweetID) {

		global $link;

		$query = "SELECT * FROM tweets WHERE id = ".$tweetID." LIMIT 1";

		$result = mysqli_query($link, $query);

		$tweet = mysqli_fetch_assoc($result);

		$userId = $tweet['userid'];


		$userQuery = "SELECT * FROM users WHERE id =".mysqli_real_escape_string($link, $userId)." LIMIT 1";

		$userQueryResult = mysqli_query($link, $userQuery);

		$user = mysqli_fetch_assoc($userQueryResult);

		if (empty($user['photo']) || !filter_var($user['photo'], FILTER_VALIDATE_URL)) {

			$photo = "./images/default.JPG";

		} else {

			$photo = $user['photo'];

		}

		echo "

		<h2>Comments</h2>";

		if ($userId == $_SESSION['id']) {

			echo "<div class='tweet'>
			<p class='myTweet'>
				<a href='?page=myprofile'><img width='50px' height='50px' src='".$photo."' alt='default user image'></a>
				<a class='' href='?page=myprofile'>".$user['username']."</a>
				<span class='time'>".time_since(time() - strtotime($tweet['datetime']))." ago</span>
				<a data-tweetId='".$tweetId."'' class='deleteTweet'>Delete</a>
			</p>";

		} else {

			echo "<div class='tweet'>
			<p>
				<a href='?page=publicprofiles&userid=".$user['id']."'><img width='50px' height='50px' src='".$photo."' alt='default user image'></a>
				<a href='?page=publicprofiles&userid=".$user['id']."'>".$user['username']."</a>
				<span class = 'time'>".time_since(time() - strtotime($tweet['datetime']))." ago</span>
			</p>";

		}

		echo "<p>".$tweet['tweet']."</p>";

			echo "<div class='numbers'>";

				//get amount of likes
				$likes = "SELECT COUNT(*) AS `number` FROM likes WHERE tweetLiked = ".$tweetID;
				$likesResults = mysqli_query($link, $likes);

				$likeNumber = mysqli_fetch_assoc($likesResults);

				echo "<a class='tweetLikes' data-tweetId='".$tweetID."'>Likes: ".$likeNumber['number']."</a>";


				//get amount of comments
				$comments = "SELECT COUNT(*) AS `number` FROM comments WHERE tweet = ".$tweetID;
				$commentsResults = mysqli_query($link, $comments);

				$commentNumber = mysqli_fetch_assoc($commentsResults);

				echo "<a class='tweetComments' data-tweetId='".$tweetId."'>Comments: ".$commentNumber['number']."</a>";


			echo "</div>"; // end of div numbers

			echo "<div class='options'>";

				//check if this image is liked by this user
				$isLiked = "SELECT * FROM likes WHERE userLiking =".mysqli_real_escape_string($link, $_SESSION['id'])." AND tweetLiked = ".$tweetID;
				$isLikedResults = mysqli_query($link, $isLiked);

				if (mysqli_num_rows($isLikedResults) > 0) {

					echo "<a class='likeTweet' data-tweetId='".$tweetID."'><img id='".$tweetID.$tweetID."' src='./images/liked.png' alt='liked image'></a>";

				} else {

					echo "<a class='likeTweet' data-tweetId='".$tweetID."'><img id='".$tweetID.$tweetID."' src='./images/unliked.png' alt='unliked image'></a>";

				
}
				echo "<span class='comment'><img id='commentImage".$tweetID."' src='./images/coloredQuote.png' alt='quote image'></span>";

			echo "</div>"; // end of div controls

		echo "</div>"; // end of tweet

		$query = "SELECT comments.id as commentID, `datetime`, user AS userID, comment, username FROM comments JOIN users ON users.id = comments.user WHERE tweet = ". mysqli_real_escape_string($link, $tweetID)." ORDER BY datetime ASC";

		$result = mysqli_query($link, $query);

		echo '<div class="comments">';

		while ($comments = mysqli_fetch_assoc($result)) {

			$response = $comments['comment'];
			$username = $comments['username'];
			$userID = $comments['userID'];
			$responseID = $comments['commentID'];

			if ($userID == $_SESSION['id']) {

				echo "<div class='response'>
				<p>
					<a href='?page=publicprofiles&userid=".$userID."'>".$username."</a>
					<span class = 'time'>".time_since(time() - strtotime($comments['datetime']))." ago</span>
					<a data-commentID='".$responseID."'' class='deleteComment'><span class='delete'>Delete</span></a>
				</p>
				<p class='responseText'>".$response."</p>";

				// <img src='./images/delete.png' alt='delete image'>

				echo "</div>";

			} else {

				echo "<div class='response'>
					<p>
						<a href='?page=publicprofiles&userid=".$userID."'>".$username."</a>
						<span class = 'time'>".time_since(time() - strtotime($comments['datetime']))." ago</span>
					</p>
					<p class='responseText'>".$response."</p>";

				echo "</div>";

			}

		}

		echo '</div>';

		echo '<div id="comment" class="commentDivs">
				
				<div id="commentSuccess" class="alert alert-success hidden">Your comment was posted successfully.</div>
				<div id="commentFail" class="alert alert-danger hidden">Your tweet was not posted.</div>
				<div class="form>
					<div class="form-group">
						<textarea name="commentContent" data-tweetId="'.$tweetID.'" class="form-control" id="commentContent" placeholder="Comment"></textarea>
					</div>
					<button id="postComment" class="btn btn-primary">Post Comment</button>
				</div>
			</div>';

	}

	function displaySearch () {

		if ($_SESSION['id'] > 0) {

		echo "<h4>Search Tweets</h4>";

		echo '
		<form class="form">
		  <div class="form-group">
		  	<input type="hidden" name="page" value="search">
		    <input type="text" name="q" class="form-control" id="search" placeholder="Search">
		  </div>
		  <button class="btn btn-primary">Search Tweets</button>
		</form>';

		} else {

			echo "<h4>Search Tweets</h4>";

			echo '
			<div class="form-inline">
			  <div class="form-group">
			  	<input type="hidden" name="page" value="search">
			    <input type="text" name="q" class="form-control" id="search" placeholder="Search">
			  </div>
			  <button class="needLogin btn btn-primary">Search Tweets</button>
			</div>';

		}

	}

	function displayTweetBox () {

		if ($_SESSION['id'] > 0) {

			echo "<h4>Post Tweet</h4>";

			echo '
				<div id="tweetSuccess" class="alert alert-success">Your tweet was posted successfully.</div>
				<div id="tweetFail" class="alert alert-danger">Your tweet was posted successfully.</div>
				<div class="form>
					<div class="form-group">
						<textarea class="form-control" id="tweetContent" placeholder=""></textarea>
					</div>
					<button id="postTweetButton" class="btn btn-primary">Post Tweet</button>
				</div>';
		} else {

			echo '
				<div class="form>
					<div class="form-group">
						<textarea class="form-control" id="tweetContent" placeholder=""></textarea>
					</div>
					<button class="needLogin btn btn-primary">Post Tweet</button>
				</div>';

		}
	}

	function displayUsers() {

		global $link;

		$query = "SELECT * FROM users";

		$result = mysqli_query($link, $query);

		while ($row = mysqli_fetch_assoc($result)) {

			if (empty($row['fname'])) {

				$fname = "";

			} else {

				$fname = $row['fname'];

			}

			if (empty($row['lname'])) {

				$lname = "";

			} else {

				$lname = $row['lname'];

			}

			if (!empty($row['lname']) || !empty($row['fname'])) {

				$text = " as ";

			} else {

				$text = "";

			}


			echo "<div class='userDiv bg-primary'><a class='text-white' href='?page=publicprofiles&userid=".$row['id']."'>".$fname." ".$lname.$text.$row['username']."</a>";
				
				$isFollowingQuery = "SELECT * FROM isfollowing WHERE follower = ". mysqli_real_escape_string($link, $_SESSION['id'])." AND isFollowing = ". mysqli_real_escape_string($link, $row['id'])." LIMIT 1";

				$isFollowingQueryResult = mysqli_query($link, $isFollowingQuery);

				if ($row['id'] != $_SESSION['id']) {

					if (mysqli_num_rows($isFollowingQueryResult) > 0) {

						echo "<a class='toggleFollow btn btn-success' data-userId='".$row['id']."'>Unfollow</a>";

					} else {

						echo "<a class='toggleFollow btn btn-success' data-userId='".$row['id']."'>Follow</a>";

					}

				}

			echo "</div>";
				
		}

	}

	function displayProfile($userID) {

		global $link;

		if (is_numeric($userID)) {

			// echo "<p>Showing results for ".mysqli_real_escape_string($link, $_GET['q'])."</p>";

			$userQuery = "SELECT * FROM users WHERE id =".mysqli_real_escape_string($link, $userID)." LIMIT 1";

			$userQueryResult = mysqli_query($link, $userQuery);

			$userData = mysqli_fetch_assoc($userQueryResult);

			$user = array(
				'id'=>mysqli_real_escape_string($link, $userData['id']), 
				'email'=>mysqli_real_escape_string($link, $userData['email']), 
				'username'=>mysqli_real_escape_string($link, $userData['username']), 
				'description'=>mysqli_real_escape_string($link, $userData['description']), 
				'photo'=>mysqli_real_escape_string($link, $userData['photo']),
				'bgphoto'=>mysqli_real_escape_string($link, $userData['bgphoto']),
				'lname'=>mysqli_real_escape_string($link, $userData['lname']),
				'fname'=>mysqli_real_escape_string($link, $userData['fname']) 
			);

			if (empty($user['bgphoto'])) {

				$bgphoto = "./images/defaultBG.png";

			} else {

				$bgphoto = $user['bgphoto'];

			}

			if (empty($user['photo'])) {

				$photo = "./images/default.JPG";

			} else {

				$photo = $user['photo'];

			}


			// Get number of users following userID
			$followersQuery = "SELECT COUNT(*) AS followers FROM isfollowing WHERE isFollowing = ".$userID;
			$followersResult = mysqli_query($link, $followersQuery);

			$followersNumber = mysqli_fetch_assoc($followersResult);

			$user['followers'] = $followersNumber['followers'];

			// Get number where userID is following
			$followingQuery = "SELECT COUNT(*) AS following FROM isfollowing WHERE follower = ".$userID;
			$followingResult = mysqli_query($link, $followingQuery);

			$followingNumber = mysqli_fetch_assoc($followingResult);

			$user['following'] = $followingNumber['following'];



			// Get number where users liked userId post
			$usersLikingQuery = "SELECT COUNT(*) AS likesCount FROM likes l JOIN tweets t ON l.tweetLiked = t.id WHERE userid = ".$userID;

			$usersLikingResult = mysqli_query($link, $usersLikingQuery);

			$usersLikingNumber = mysqli_fetch_assoc($usersLikingResult);

			$user['likesCount'] = $usersLikingNumber['likesCount'];

			// Get number where users liked userId post
			$totalTweetsQuery = "SELECT COUNT(*) AS totalTweets FROM tweets WHERE userid = ".$userID;

			$totalTweetsResult = mysqli_query($link, $totalTweetsQuery);

			$totalTweetsNumber = mysqli_fetch_assoc($totalTweetsResult);

			$user['totalTweets'] = $totalTweetsNumber['totalTweets'];

			echo "<div class='col-md-1'></div>
			<div class='userProfile mx-0 col-lg-10'>

					<div class='profileImage'>

						<img src='".$bgphoto."' alt='default user image'>
		
					</div>";

			echo "<div class='profileHeader'>
							
						<p class='userPicture'><img width='80px' height='80px' src='".$photo."' alt='default user image'></p>
						<p class='userUsername data'>".$user['username']."</p>
						<p class='userFollowing data'>Following: ".$user['following']."</p>
						<p class='userFollowers data'>Followers: ".$user['followers']."</p>
						<p class='userTweets data'>Tweets: ".$user['totalTweets']."</p>
						<p class='userLikes data'>Likes: ".$user['likesCount']."</p>
						<p class='userButton'>";

							if ($userID == $_SESSION['id'] && (empty($user['fname']) || empty($user['lname']) || empty($user['description']))) {

								echo '<a href="?page=settings&user='.$_SESSION['id'].'" id="account" class="btn btn-success">Finish Account Setup</a>';

							} else if ($userID == $_SESSION['id']) {

								echo '<a href="?page=settings&user='.$_SESSION['id'].'" id="account" class="btn btn-success">Account Settings</a>';
							} else {

								$isFollowingQuery = "SELECT * FROM isfollowing WHERE follower = ". mysqli_real_escape_string($link, $_SESSION['id'])." AND isFollowing = ". mysqli_real_escape_string($link, $userID)." LIMIT 1";

								$isFollowingQueryResult = mysqli_query($link, $isFollowingQuery);

								if ($userID != $_SESSION['id']) {

									if (mysqli_num_rows($isFollowingQueryResult) > 0) {

										echo "<a class='toggleFollow btn btn-success' data-userId='".$userID."'>Unfollow</a>";

									} else {

										echo "<a class='toggleFollow btn btn-success' data-userId='".$userID."'>Follow</a>";

									}

								}

							}

							echo "</p>

				</div>

				<div class='profileOther'>

					<div class='profileDetails'>

						<p class='userName'>".$user['fname']." ".$user['lname']."</p>
						<p class='userDescription'>".$user['description']."</p>

					</div>";

				echo "<div class='row mx-0 bg-white col-md-12'>
				";

				if ($user['id'] == $_SESSION['id']) {

					echo "<div class='col-lg-6'>";

						displayNotifications($_SESSION['id']);
						displayTweetBox();

					//This was acting wierd. It should need an ending div, but if you add one, it outputs two. 
					//Neither of the two functions an extra div so I don't know. 
					// echo "</div>";
					echo "<div class='col-lg-6 profileTweets'>";

						displayTweets($userID);
								
					echo "</div>";

				} else {

					displayTweets($userID);

				}

				echo "</div>
				</div>
				<div class='col-md-1'></div>
				</div>";


		}

	}

	function displayNotifications($userID) {

		echo "<h4>Notifications</h4>";

		echo "<p>Notifications coming soon!</p>";

	}

	function displaySettings($userID) {

		global $link;

		$userQuery = "SELECT * FROM users WHERE id =".mysqli_real_escape_string($link, $userID)." LIMIT 1";

		$userQueryResult = mysqli_query($link, $userQuery);

		$userData = mysqli_fetch_assoc($userQueryResult);

		$user = array(
			'id'=>mysqli_real_escape_string($link, $userData['id']), 
			'email'=>mysqli_real_escape_string($link, $userData['email']), 
			'username'=>mysqli_real_escape_string($link, $userData['username']), 
			'description'=>mysqli_real_escape_string($link, $userData['description']), 
			'photo'=>mysqli_real_escape_string($link, $userData['photo']),
			'bgphoto'=>mysqli_real_escape_string($link, $userData['bgphoto']),
			'lname'=>mysqli_real_escape_string($link, $userData['lname']),
			'fname'=>mysqli_real_escape_string($link, $userData['fname']),
			'notifs'=>mysqli_real_escape_string($link, $userData['notifs']) 
		);

		//for ease of change later;
		$nothing = '';

		echo '

			<div class="form settingsForm">

				<h3 id="changedName">Settings for '.$user["username"].'</h3>

				<div id="formErrorAlert" class="hidden alert alert-dismissible alert-danger">
					<button id="closeErrorAlert" type="button" class="close">&times;</button>
					<div id="formError"></div>
				</div>

				<div id="formSuccessAlert" class="hidden alert alert-dismissible alert-success">
					<button id="closeSuccessAlert" type="button" class="close">&times;</button>
					<div id="formSuccess"></div>
				</div>

				<input type="hidden" value="'.$user['id'].'" id="sid"">';

				if ($user['email'] == "view@view.com") {

					echo '
					<div class="form-group hidden">
						<label for="semail">Email address</label>
						<input type="email" class="form-control" value="'.$user['email'].'" id="semail" aria-describedby="semailHelp" placeholder="Email" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
						<small id="semailHelp" class="form-text text-muted">'.$nothing.'</small>
	    			</div>';

				} else {

					echo '
					<div class="form-group">
						<label for="semail">Email address</label>
						<input type="email" class="form-control" value="'.$user['email'].'" id="semail" aria-describedby="semailHelp" placeholder="Email" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
						<small id="semailHelp" class="form-text text-muted">'.$nothing.'</small>
	    			</div>';

				}

				echo '
    			<div class="form-group">
					<label for="susername">Username</label>
					<input type="text" maxlength="10" class="form-control" value="'.$user['username'].'" id="susername" aria-describedby="susernameHelp" placeholder="Username" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
					<small id="susernameHelp" class="form-text text-muted">'.$nothing.'</small>
    			</div>

    			<div class="form-group">
					<label for="sfname">First Name</label>
					<input type="text" class="form-control" id="sfname" value="'.$user['fname'].'" aria-describedby="sfnameHelp" placeholder="First name" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
					<small id="sfnameHelp" class="form-text text-muted">'.$nothing.'</small>
    			</div>

    			<div class="form-group">
					<label for="slname">Last Name</label>
					<input type="text" class="form-control" id="slname" value="'.$user['lname'].'" aria-describedby="slnameHelp" placeholder="Last name" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
					<small id="slnameHelp" class="form-text text-muted">'.$nothing.'</small>
    			</div>

    			<div class="form-group">
					<label for="sdescription">Description</label>
					<textarea class="form-control" id="sdescription" rows="3">'.$user['description'].'</textarea>
    			</div>

    			<div class="form-group">
					<label for="spimage">Profile Image</label>
					<input type="text" class="form-control" id="spimage" value="'.$user['photo'].'" aria-describedby="spimageHelp" placeholder="Profile Image URL" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
					<small id="spimageHelp" class="form-text text-muted">'.$nothing.'A default image is set, leave this empty to continue to use that or add a URL.  200px by 200px works best.</small>
    			</div>

    			<div class="form-group">
					<label for="sbgimage">Profile Image</label>
					<input type="sbgimage" class="form-control" id="sbgimage" value="'.$user['bgphoto'].'" aria-describedby="sbgimageHelp" placeholder="Profile Background Image URL" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
					<small id="sbgimageHelp" class="form-text text-muted">'.$nothing.'A default image is set, leave this empty to continue to use that or add a URL.  900px by 200px works best.</small>
    			</div>';
	
				if ($user['notifs'] == 1) {

					echo '<div class="form-check">
				        <label class="form-check-label">
				          	<input class="form-check-input" type="checkbox" id="notifs" name="notifs" checked="checked" wtx-context="EF0DBA92-76C1-477B-890F-2738C79F6FC3">
				          	Would you like to receive emails?
				        </label>
				      </div>
				      <br>
					';

				} else {

					echo '<div class="form-check">
				        <label class="form-check-label">
				          	<input class="form-check-input" type="checkbox" id="notifs" name="notifs" wtx-context="EF0DBA92-76C1-477B-890F-2738C79F6FC3">
				          	Would you like to receive emails?
				        </label>
				      </div>
				      <br>
					';

				}


		     echo '<button class="btn btn-primary submitSettings">Save Changes</button>

			</div>';

	}




 ?>