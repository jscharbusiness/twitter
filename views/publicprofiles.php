<div class="container mainContainer">
	

	<?php if ($_GET['userid']) { 

		echo '<div class="col-12 row mx-0">';

			if ($_GET['userid'] == $_SESSION['id']) {

				header('Location: ?page=myprofile');

			} else {

				displayProfile($_GET['userid']); 

			}

		echo '</div>';

		} else { ?>

	<h2 class="mainH2">Active Users</h2>

	<?php displayUsers(); ?>

	<?php } ?>

	</div>

</div>
