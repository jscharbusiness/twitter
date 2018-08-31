<div class="container mainContainer">
	
	<div class="row mx-0">

			<div class="col-1"></div>
			<div class="col-10">

			<?php if ($_GET['user']) { 

					if ($_GET['user'] != $_SESSION['id']) {

						header('Location: ?page=myprofile');

					} else {

						displaySettings($_GET['user']); 

					}

				} else { ?>


			<?php header('Location: ?page=myprofile'); ?>

			<?php } ?>

	</div>
	<div class="col-1"></div>

	</div>

</div>
