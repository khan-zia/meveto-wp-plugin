<!DOCTYPE html>
<html>
<head>
	<title>Meveto user was not found on this website</title>
	<link rel='stylesheet' id='meveto-no-user-css'  href='<?= plugins_url('../assets/css/no_user.css', __DIR__); ?>' type='text/css' media='all' />
</head>
<body class="meveto-body">
	<div class="meveto-form-wrapper">
		<h2>Connect your Meveto account with your account on this website</h2>
		<p>
			Meveto could not log you in at the moment because it seems your Meveto account is not connected to any account on this website. If you already have an account on this website, you can connect it to your Meveto account by simply filling the form below.
		</p>
		<div class="meveto-container">
			<?php
	            if(isset($_SESSION['meveto_error'])) {
				?>
					<div class="meveto-error">
						<?php echo $_SESSION['error']; ?>
					</div>
				<?php
				unset($_SESSION['meveto_error']);
			}
			?>
		    <form method="post" action="<?php echo home_url().'/meveto/connect?meveto_id='.$_GET['meveto_id']; ?>">
		    	<div class="meveto-form-element">
		    		<label for="login_name">Your login email/username</label>
		    		<input
			    		type="text"
			    		name="login_name"
			    		id="login_name"
		    			placeholder="Your login email/username on this website"
		    			required
		    		>
		    	</div>
		    	<div class="meveto-form-element">
		    		<label for="login_password">Your password</label>
		    		<input
			    		type="password"
			    		name="login_password"
			    		id="login_password"
		    			placeholder="Your login password on this website"
		    			required
		    		>
		    	</div>
		    	<div class="meveto-form-element">
		    		<button type="submit" class="meveto-button">Connect to Meveto</button>
		    	</div>
		    </form>
		</div>
	</div>

</body>
</html>
