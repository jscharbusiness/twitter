	<footer class="footer navbar-dark bg-primary">
		<div class="container">
			<p>&copy; 2018 Jeremy Schar </p>
		</div>
	</footer>
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<script>
		
		$("#toggleLogin").click(function(){

			if($("#loginActive").val() == "1") {

				$("#loginActive").val("0");
				$("#loginModalTitle").html("Sign Up");
				$("#loginSignupButton").html("Sign Up");
				$("#toggleLogin").html("Login");
				$("#usernameDiv").removeClass('d-none');

			} else {

				$("#loginActive").val("1");
				$("#loginModalTitle").html("Login");
				$("#loginSignupButton").html("Login");
				$("#toggleLogin").html("Sign Up");
				$("#usernameDiv").addClass('d-none');
			}

		});

		$("#loginSignupButton").click(function(){

			$.ajax({
				type: "POST",
				url: "actions.php?action=loginSignup",
				data: "&email=" + $("#email").val() + "&password=" + $("#password").val() + "&username=" + $("#username").val() + "&loginActive=" + $("#loginActive").val(),
				success: function(result) {
					if (result == "1") {

						window.location.assign("http://localhost:8080/twitter/");

					} else {

						$("#loginAlert").html(result).show();

					}
				}
			});

		});
		
		$(document).on('keyup', "#password", function(e) {

			if (e.keyCode == 13) {

				$.ajax({
					type: "POST",
					url: "actions.php?action=loginSignup",
					data: "&email=" + encodeURIComponent($("#email").val()) + "&password=" + encodeURIComponent($("#password").val()) + "&libraryName=" + encodeURIComponent($("#libraryName").val()) + "&loginActive=" + encodeURIComponent($("#loginActive").val()),
					success: function(result) {
						if (result == "1") {

							window.location.assign('<?php echo $SITEURL; ?>');

						} else {

							$("#loginAlert").html(result).show();

						}
					}
				});

			}

		});

		$(".toggleFollow").click(function() {

			var id = $(this).attr("data-userId");

			$.ajax({
				type: "POST",
				url: "actions.php?action=toggleFollow",
				data: "userId=" + id,
				success: function(result) {

					if (result == "1") {

						$("a[data-userId='" + id + "']").html("Follow");

					} else if (result == "2") {

						$("a[data-userId='" + id + "']").html("Unfollow");

					}
				
				}
			});

		});

		$("#postTweetButton").click(function() {

			$.ajax({
				type: "POST",
				url: "actions.php?action=postTweet",
				data: "tweetContent=" + $("#tweetContent").val(),
				success: function(result) {

					if (result == "1") {

						window.location.reload();

					} else if (result != "") {

						$("#tweetFail").html(result).show();
						$("#tweetSuccess").hide();

					}
				
				}
			});

		});

		$(".deleteTweet").click(function() {

			var confirmDelete = confirm("Do you really want to delete this?");

			if (confirmDelete == true) {

				var id = $(this).attr("data-tweetId");

			    $.ajax({
					type: "POST",
					url: "actions.php?action=deleteTweet",
					data: "tweetId=" + id,
					success: function(result) {

						if (result == "1") {

							window.location.reload();

						} else if (result != "") {

							$("#tweetFail").html("Your tweet could not be deleted!").show();
							$("#tweetSuccess").hide();

						}
					
					}
				});

			}

		});

		$(".likeTweet").click(function() {

			var tweetId = $(this).attr("data-tweetId");

			var imgId = tweetId + tweetId;

		    $.ajax({
				type: "POST",
				url: "actions.php?action=toggleLike",
				data: "tweetId=" + tweetId,
				success: function(result) {

					// 1 is delete, 2 is insert

					if (result == "1") {

						// $("#1723").attr("src","./images/unliked.png");

						$("#"+imgId).attr("src","./images/unliked.png");
						$("#"+imgId).attr("alt","Unliked Tweet");

					} else if (result == "2") {

						// $("#1723").attr("src","./images/liked.png");

						$("#"+imgId).attr("src","./images/liked.png");
						$("#"+imgId).attr("alt","Liked Tweet");

					}
				}
			});

		});

		$("#postComment").click(function() {

			var tweetId = $("#commentContent").attr("data-tweetId");

			$.ajax({
				type: "POST",
				url: "actions.php?action=postComment",
				data: "commentContent=" + $("#commentContent").val() + "&tweetID=" + tweetId,
				success: function(result) {

					if (result == "1") {

						window.location.reload();

					} else if (result != "") {

						$("#commentFail").html(result).show();
						$("#commentSuccess").hide();

					}
				
				}
			});

		});

		$(".deleteComment").click(function() {

			var confirmDelete = confirm("Do you really want to delete this?");

			if (confirmDelete == true) {

				var id = $(this).attr("data-commentID");

			    $.ajax({
					type: "POST",
					url: "actions.php?action=deleteComment",
					data: "commentID=" + id,
					success: function(result) {

						if (result == "1") {

							window.location.reload();

						} else if (result != "") {

							$("#commentFail").html("Your tweet could not be deleted!").show();
							$("#commentSuccess").hide();

						}
					
					}
				});

			}

		});


		$(".submitSettings").click(function() {

			var error = "go";

			if ($("#semail").val() == "") {

				$("#semailHelp").html("This email cannot be empty!");

				error = "stop";

			}

			if ($("#susername").val() == "") {

				$("#susernameHelp").html("This username cannot be empty!");

				error = "stop";

			}

			if (error == "go") {

				var sid = $("#sid").val();
				var semail = $("#semail").val();
				var susername = $("#susername").val();
				var sfname = $("#sfname").val();
				var slname = $("#slname").val();
				var sdescription = $("#sdescription").val();
				var spimage = $("#spimage").val();
				var sbgimage = $("#sbgimage").val();
				var notifs = 0;

				if ($("#notifs").prop('checked') == true) {

					notifs = 1;

				} else {

					notifs = 0;

				}

				// alert(semail + " " + susername + " " + sfname + " " + slname + " " + sdescription + " " + spimage + " " + sbgimage + " " + notifs);

				$.ajax({
					type: "POST",
					url: "actions.php?action=saveSettings",
					data: "sid=" + sid + "&semail=" + semail + "&susername=" + susername + "&sfname=" + sfname + "&slname=" + slname + "&sdescription=" + sdescription + "&spimage=" + spimage + "&sbgimage=" + sbgimage + "&notifs=" + notifs,
					success: function(result) {

						// They changed the id and cant do that.
						if (result == "100") {

							window.location.reload();

						} else if (result == "1") {

							$("#formErrorAlert").show();
							$("#formError").html("This email cannot be empty!");
							$("#semailHelp").html("This email cannot be empty!");

						} else if (result == "2") {

							$("#formErrorAlert").show();
							$("#formError").html("This username cannot be empty!");
							$("#susernameHelp").html("This username cannot be empty!");

						} else if (result == "3") {

							$("#formErrorAlert").show();
							$("#formError").html("Enter a valid email!");
							$("#semailHelp").html("Enter a valid email!");
							
						} else if (result == "4") {

							$("#formErrorAlert").show();
							$("#formError").html("This email address is already taken!");
							$("#semailHelp").html("This email address is already taken!");

						} else if (result == "5") {

							$("#formErrorAlert").show();
							$("#formError").html("This username is already taken!");
							$("#susernameHelp").html("This username is already taken!");
							
						} else if (result == "6") {

							$("#formErrorAlert").show();
							$("#formError").html("The profile image URL is invalid!");
							$("#spimageHelp").html("This URL is invalid!");

						} else if (result == "7") {

							$("#formErrorAlert").show();
							$("#formError").html("The background image URL is invalid!");
							$("#sbgimageHelp").html("This URL is invalid!");
							
						} else if (result == "8") {

							$("#formErrorAlert").show();
							$("#formError").html("Your changes could not be saved!");

						} else if (result == "10") {

							$("#changedName").html("Settings for " + susername);
							$("#formSuccessAlert").show();
							$("#formSuccess").html("Your changes have been saved!");

							$('html,body').animate({ scrollTop: 0 }, 'slow');

						} else if (result == "11") {

							$("#formErrorAlert").show();
							$("#formError").html("Your username must be less than 10 characters!");
							$("#susernameHelp").html("Your username must be less than 10 characters!");

						}
					
					}
				});

			}

		});

		$('#closeErrorAlert').click(function() {

			$("#formErrorAlert").hide();

		});

		// <div class="form settingsForm">

		// 		<h3>Settings for '.$user["username"].'</h3>

		// 		<div class="form-group">
		// 			<label for="semail">Email address</label>
		// 			<input type="semail" disabled class="form-control" value="'.$user['email'].'" id="semail" aria-describedby="semailHelp" placeholder="Email" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="$("#semail").val()" class="form-text text-muted">'.$nothing.'</small>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="susername">Username</label>
		// 			<input type="susername" disabled class="form-control" value="'.$user['username'].'" id="susername" aria-describedby="susernameHelp" placeholder="Username" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="susernameHelp" class="form-text text-muted">'.$nothing.'</small>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="sfname">First Name</label>
		// 			<input type="sfname" class="form-control" id="sfname" value="'.$user['fname'].'" aria-describedby="sfnameHelp" placeholder="First name" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="sfnameHelp" class="form-text text-muted">'.$nothing.'</small>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="slname">Last Name</label>
		// 			<input type="slname" class="form-control" id="slname" value="'.$user['lname'].'" aria-describedby="slnameHelp" placeholder="Last name" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="slnameHelp" class="form-text text-muted">'.$nothing.'</small>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="sdescription">Description</label>
		// 			<textarea class="form-control" id="sdescription" rows="3">'.$user['description'].'</textarea>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="spimage">Profile Image</label>
		// 			<input type="spimage" class="form-control" id="spimage" value="'.$user['photo'].'" aria-describedby="spimageHelp" placeholder="Profile Image URL" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="spimageHelp" class="form-text text-muted">'.$nothing.'A default image is set, leave this empty to continue to use that or add a URL.  200px by 200px works best.</small>
  //   			</div>

  //   			<div class="form-group">
		// 			<label for="sbgimage">Profile Image</label>
		// 			<input type="sbgimage" class="form-control" id="sbgimage" value="'.$user['bgphoto'].'" aria-describedby="sbgimageHelp" placeholder="Profile Background Image URL" wtx-context="03649516-6EC4-4696-875D-C990A0BD6217">
		// 			<small id="sbgimageHelp" class="form-text text-muted">'.$nothing.'A default image is set, leave this empty to continue to use that or add a URL.  900px by 200px works best.</small>
  //   			</div>

  //   			<div class="form-check">
		// 	        <label class="form-check-label">
		// 	          	<input class="form-check-input" type="checkbox" value="'.$nothing.'" wtx-context="EF0DBA92-76C1-477B-890F-2738C79F6FC3">
		// 	          	Would you like to receive emails?
		// 	        </label>
		// 	      </div>
		// 	      <br>

		//       <button class="btn btn-primary submitSettings">Save Changes</button>

		// 	</div>';




		// Show up to 10 and output a load more button. on load more button ajax for tweets where id or date is greater than the last Id or date stored in the load more button.

		//styles.

		// make likes count update on click

		// add change password





		//check search query


		//ADD IN FUTURE

		// click on likes, comments, followers, and following to see the people doing it.

		// better search.        displayTweets(type = search)

		// add better pagination to comments and tweet pages.

		// better tweet typing and html inputs

		// better images, not only a link

		// tagging other users

		// private accounts

		// better public profiles page













	</script>
  </body>
</html>