<?php 

	include("functions.php");

	include("views/header.php");

	if (isset($_SESSION['id'])) {

		include("views/nav.php");

	}

	if ($_GET['page'] == "timeline" && isset($_SESSION['id'])) {

		include("views/timeline.php");

	} else if ($_GET['page'] == "search" && isset($_SESSION['id'])) {

		include("views/search.php");

	} else if ($_GET['page'] == "settings" && isset($_SESSION['id'])) {

		include("views/settings.php");

	} else if ($_GET['page'] == "publicprofiles" && isset($_SESSION['id'])) {

		include("views/publicprofiles.php");

	} else if ($_GET['page'] == "myprofile" && isset($_SESSION['id'])) {

		include("views/myprofile.php");

	} else if ($_GET['page'] == "comments" && isset($_SESSION['id'])) {

		include("views/comments.php");

	} else {

		if (isset($_SESSION['id'])) {

			include("views/home.php");

		} else {

			include("views/login.php");

		}
		
	}

	//Removed as they can see the posts in their profile
	//  else if ($_GET['page'] == "yourtweets") {

	// 	include("views/yourtweets.php");

	// }

	include("views/footer.php");

 ?>