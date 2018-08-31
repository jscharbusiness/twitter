<div class="container mainContainer">

	<h2 class="mainH2">Recent Tweets</h2>
	
	<div class="row mx-0">

		<div class="col-lg-1 col-sm-1"></div>
		<div class="col-lg-4 col-sm-10">
			
			<?php displaySearch(); ?>

			<hr>

			<?php displayTweetBox(); ?>


		<!-- div ending not needed...it only glitches everything -->
		<div class="col-lg-7 tweetBox">

			<?php displayTweets('public'); ?>

		</div>


	</div>



</div>
