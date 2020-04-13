<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Vincy">
<link rel="stylesheet" type="text/css"
	href="assets/css/phppot-style.css">
<title>Double Opt-In Subscription Form with Secure Hash using PHP</title>
</head>
<body>
	<div class="phppot-container">
		<h1>Double Opt-in Subscription</h1>
		<form class="phppot-form" action="" method="POST">
			<div class="phppot-row">
				<div class="label">
					Name
				</div>
				<input type="text" id="pp-name" name="pp-name"
					class="phppot-input">
			</div>
			<div class="phppot-row">
				<div class="label">
					Email *
					<div id="email-info" class="validation-message"
						data-required-message="required."
						data-validate-message="Invalid email."></div>
				</div>
				<input type="text" id="pp-email" name="pp-email"
					class="required email phppot-input"
					onfocusout="return validateEmail();">
			</div>
			<div class="phppot-row">
				<button type="Submit" id="phppot-btn-send">Subscribe</button>
				<div id="phppot-loader-icon">Sending ...</div>
				<div id="phppot-message"></div>
			</div>
		</form>
	</div>
<script src="vendor/jquery/jquery-3.3.1.js"></script>
<script src="assets/js/subscribe.js"></script></body>	
</body>
</html>