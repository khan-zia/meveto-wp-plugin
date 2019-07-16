<!DOCTYPE html>
<html>
<head>
	<title>Meveto user was not found on this website</title>
	<link rel='stylesheet' id='meveto-no-user-css'  href='<?=plugins_url() . '/meveto-login/public/css/no_user.css'?>' type='text/css' media='all' />
</head>
<body>
	<div class="meveto-form-wrapper">
		<h2>Synchronize your Meveto's email/username with your email/username on this website</h2>
		<p>
			We are sorry, Meveto could not log you in at the moment becuase it seems your Meveto account's email is not registered with this website yet. If you already have an account on this website with another email/username, you can connect it to your Meveto account very easily by filling the form below.
		</p>
		<div class="container">
			<?php
                error_log("\n no_user:".$_GET['token']."\n The above is JWT token on /no_user.",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
	            if(isset($_SESSION['meveto_error'])) {
				?>
					<div class="meveto-error">
						<?php echo $_SESSION['error']; ?>
					</div>
				<?php
				unset($_SESSION['meveto_error']);
			}
			?>
		    <form method="post" action="<?php echo home_url().'/meveto/connect?token='.$_GET['token']; ?>">
		    	<div class="form-element">
		    		<label for="login_name">Your login email/username</label>
		    		<input
			    		type="text"
			    		name="login_name"
			    		id="login_name"
		    			placeholder="Your login email/username on this website"
		    			required
		    		>
		    	</div>
		    	<div class="form-element">
		    		<label for="login_password">Your password</label>
		    		<input
			    		type="password"
			    		name="login_password"
			    		id="login_password"
		    			placeholder="Your login password on this website"
		    			required
		    		>
		    	</div>
		    	<div class="form-element">
		    		<button type="submit" class="meveto-button">Connect to Meveto</button>
		    	</div>
		    </form>
		</div>
	</div>

</body>
</html>
