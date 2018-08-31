<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<a class="navbar-brand" href="http://localhost:8080/twitter/">Twitter</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav mr-auto">
	    	<?php if ($_SESSION['id']) { ?>
		    	<li class="nav-item">
			        <a class="nav-link" href="?page=timeline">Your timeline</a>
			    </li>
				<li class="nav-item">
					<a class="nav-link" href="?page=publicprofiles">Public Profiles</a>
				</li>
		    	<li class="nav-item">
			        <a class="nav-link" href="?page=myprofile">My Profile</a>
			    </li>
			<?php } else { ?>
				<li class="nav-item">
			        <span class="needLogin nav-link" href="?page=timeline">Your timeline</span>
			    </li>
				<li class="nav-item">
					<span class="needLogin nav-link" href="?page=publicprofiles">Public Profiles</span>
				</li>
		    	<li class="nav-item">
			        <span class="needLogin nav-link" href="?page=myprofile">My Profile</span>
			    </li>
			<?php } ?>
	    </ul>
	    <div class="form-inline my-2 my-lg-0">
	    	<?php if ($_SESSION['id']) { ?>
	    		
				<a class="btn btn-outline-success" href="?function=logout" >Logout</a>

	    	<?php } else { ?>
	      
	      		<button class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal" data-target="#loginModal">Login/Sign Up</button>

	      	<?php } ?>
	    </div>
	</div>
</nav>