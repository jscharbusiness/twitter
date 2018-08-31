<div class="container mainContainer">
	
	<div class="row">

		<div class="col-12">

			<?php if ($_GET['tweet']) { ?>

			<?php displayComments($_GET['tweet']); ?>

			<?php } ?>

		</div>

		<!-- <div class="col-4">
			<?php displaySearch(); ?>

			<hr>

			<?php displayTweetBox(); ?>
			
		</div> -->

	</div>

</div>



