<?php 

	include("functions.php");

	if ($_GET['action'] == "loginSignup") {

		$error = "";

		if (!$_POST['email']) {

			$error = "An email address is required.";

		} else if ($_POST['loginActive'] == "0" && !$_POST['username']) {

			$error = "A username is required.";

		} else if (strlen($susername) > 10) {

			$error = "Your username must be less than 10 characters!";

		} else if (!$_POST['password']) {

			$error = "A password is required.";

		} else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {

			$error = "Please enter a valid email address."; 

		} 

		if ($error != "") {

			echo $error;
			exit();

		}

		if ($_POST['loginActive'] == "0") {

			$query = "SELECT * FROM users where email ='". mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
			$result = mysqli_query($link, $query);

			$usernameCheck = "SELECT * FROM users where username ='". mysqli_real_escape_string($link, $_POST['username'])."' LIMIT 1";
			$usernameResult = mysqli_query($link, $usernameCheck);

			if (mysqli_num_rows($result) > 0) { 

				$error = "This email address is already taken.";

			} else if (mysqli_num_rows($usernameResult) > 0) {

				$error = "This username is already taken.";

			} else {

				$query = "INSERT INTO users (email, password, username) VALUES ('". mysqli_real_escape_string($link, test($_POST['email']))."', '". mysqli_real_escape_string($link, test($_POST['password']))."', '". mysqli_real_escape_string($link, test($_POST['username']))."')";

				if (mysqli_query($link, $query)) {

					$_SESSION['id'] = mysqli_insert_id($link);

					$query = "UPDATE users SET password = '". md5(md5($_SESSION['id']) .$_POST['password']) . "'WHERE id = ". $_SESSION['id']." LIMIT 1";

					mysqli_query($link, $query);

					echo 1;

				} else {

					$error = "Couldn't create user. Please try again later.";

				}

			}

		} else {

			$query = "SELECT * FROM users where email ='". mysqli_real_escape_string($link, $_POST[email])."' LIMIT 1";

			$result = mysqli_query($link, $query);

			$row = mysqli_fetch_assoc($result);

				if ($row['password'] == md5(md5($row['id']).$_POST['password'])) {

					echo 1;

					$_SESSION['id'] = $row['id'];

				} else {

					$error = "Could not find that email/password combination. Please try again.";

				} 

		}

		if ($error != "") {

			echo $error;
			exit();

		}

	}

	if ($_GET['action'] == 'toggleFollow') {

		$query = "SELECT * FROM isfollowing WHERE follower = ". mysqli_real_escape_string($link, $_SESSION['id'])." AND isFollowing = ". mysqli_real_escape_string($link, $_POST['userId'])." LIMIT 1";
		$result = mysqli_query($link, $query);

		if (mysqli_num_rows($result) > 0) {

			$row = mysqli_fetch_assoc($result);

			mysqli_query($link, "DELETE FROM isfollowing WHERE id = ". mysqli_real_escape_string($link, $row['id'])." LIMIT 1");

			echo "1";

		} else {

			mysqli_query($link, "INSERT INTO isfollowing (follower, isFollowing) VALUES (". mysqli_real_escape_string($link, $_SESSION['id']).", ". mysqli_real_escape_string($link, test($_POST['userId'])).")");

			echo "2";

		}

	}

	if ($_GET['action'] == 'deleteTweet') {

		$query = "DELETE FROM tweets WHERE id = ". mysqli_real_escape_string($link, $_POST['tweetId'])." LIMIT 1";

		$result = mysqli_query($link, $query);

		if (mysqli_affected_rows($link) > 0) {

			$query = "DELETE FROM likes WHERE tweetLiked = ". mysqli_real_escape_string($link, $_POST['tweetId']);

			$result = mysqli_query($link, $query);

			$query = "DELETE FROM comments WHERE tweet = ". mysqli_real_escape_string($link, $_POST['tweetId']);

			$result = mysqli_query($link, $query);

			echo "1";

		} else {

			echo "2";

		}

	}

	if ($_GET['action'] == 'deleteComment') {

		$query = "DELETE FROM comments WHERE id = ". mysqli_real_escape_string($link, $_POST['commentID'])." LIMIT 1";

		$result = mysqli_query($link, $query);

		if (mysqli_affected_rows($link) > 0) {

			echo "1";

		} else {

			echo "2";

		}

	}


	if ($_GET['action'] == 'toggleLike') {

		$query = "SELECT * FROM likes WHERE userLiking = ". mysqli_real_escape_string($link, $_SESSION['id'])." AND tweetLiked = ". mysqli_real_escape_string($link, $_POST['tweetId'])." LIMIT 1";
		$result = mysqli_query($link, $query);

		if (mysqli_num_rows($result) > 0) {

			$row = mysqli_fetch_assoc($result);

			mysqli_query($link, "DELETE FROM likes WHERE id = ". mysqli_real_escape_string($link, $row['id'])." LIMIT 1");

			//deleted
			echo "1";

		} else {

			$query = "INSERT INTO likes (userLiking, tweetLiked) VALUES (". mysqli_real_escape_string($link, $_SESSION['id']).", ". mysqli_real_escape_string($link, test($_POST['tweetId'])).")";
			$result = mysqli_query($link, $query);

			//inserted
			echo "2";


		}

	}


	if ($_GET['action'] == 'postTweet') {

		if (!$_POST['tweetContent']) {

			echo "Your tweet is empty!";

		} else if (strlen($_POST['tweetContent']) > 140) {

			echo "Your tweet is too long!";

		} else {

			mysqli_query($link, "INSERT INTO tweets (`tweet`, `userid`, `datetime`) VALUES ('". mysqli_real_escape_string($link, test($_POST['tweetContent']))."', '". mysqli_real_escape_string($link, $_SESSION['id'])."', NOW())");

			echo "1";

		}

	}

	if ($_GET['action'] == 'postComment') {

		if (!$_POST['commentContent']) {

			echo "Your comment is empty!";

		} else {

			$query =  "INSERT INTO comments (`comment`, `user`, `tweet`, `datetime`) VALUES ('". mysqli_real_escape_string($link, test($_POST['commentContent']))."', '". mysqli_real_escape_string($link, $_SESSION['id'])."', '". mysqli_real_escape_string($link, test($_POST['tweetID']))."', NOW())";
			mysqli_query($link, $query);

			echo "1";

		}

	}

	if ($_GET['action'] == "saveSettings") {

		if ($_POST['sid'] != $_SESSION['id']) {

			echo "100";
			exit();

		} else {

			$query = "SELECT * FROM users where email ='". mysqli_real_escape_string($link, $_POST['semail'])."' LIMIT 1";
			$result = mysqli_query($link, $query);
			$row = mysqli_fetch_assoc($result);

			$userEmail = $row['email'];
			$userUsername = $row['username'];

			$sid = test($_POST['sid']);
			$semail = test($_POST['semail']);
			$susername = test($_POST['susername']);
			$sfname = test($_POST['sfname']);
			$slname = test($_POST['slname']);
			$sdescription = test($_POST['sdescription']);
			$spimage = test($_POST['spimage']);
			$sbgimage = test($_POST['sbgimage']);
			$snotifs = test($_POST['notifs']);

			if (!$semail) {

				echo "1";
				exit();

			} else if (!$susername) {

				echo "2";
				exit();

			} else if (strlen($susername) > 10) {

				echo "11";
				exit();

			} else if (filter_var($semail, FILTER_VALIDATE_EMAIL) === false) {

				echo "3";
				exit();

			} else if (!empty($spimage) && filter_var($spimage, FILTER_VALIDATE_URL) === false) {

				echo "6";
				exit();

			} else if (!empty($sbgimage) && filter_var($sbgimage, FILTER_VALIDATE_URL) === false) {

				echo "7";
				exit();

			}


			if ($semail != $userEmail) {

				$query = "SELECT * FROM users where email ='". mysqli_real_escape_string($link, $semail)."' LIMIT 1";
				$result = mysqli_query($link, $query);

				if (mysqli_num_rows($result) > 0) {

					echo "4";
					exit();

				}

			}

			if ($susername != $userUsername) {

				$query = "SELECT * FROM users where username ='". mysqli_real_escape_string($link, $susername)."' LIMIT 1";
				$result = mysqli_query($link, $query);

				if (mysqli_num_rows($result) > 0) {

					echo "5";
					exit();

				}

			}

			if (empty($sdescription)) {

				$description = " description = '', ";

			} else {

				$description = " description = '".$sdescription."', ";

			}

			if (empty($sfname)) {

				$fname =  " fname = '',";

			} else {

				$fname = " fname = '".$sfname."', ";

			}

			if (empty($slname)) {

				$lname = " lname = '',";

			} else {

				$lname = " lname = '".$slname."', ";

			}

			if (empty($spimage)) {

				$photo = " photo = '', ";

			} else {

				$photo = " photo = '".$spimage."', ";

			}

			if (empty($sbgimage)) {

				$bgphoto = " bgphoto = '', ";

			} else {

				$bgphoto = " bgphoto = '".$sbgimage."', ";

			}

			if ($snotifs == 1 ) {

				$notifs = " notifs = 1 ";

			} else {

				$notifs = " notifs = 0 ";

			}

				
			$updateQuery = "UPDATE users SET email = '".$semail."', username = '".$susername."', $fname $lname $description $photo $bgphoto $notifs WHERE id = ".$sid." ";

			if (mysqli_query($link, $updateQuery)) {

				echo "10";
				exit();

			} else {

				echo "8     ".$updateQuery;
				exit();

			}

		}

	}




 ?>