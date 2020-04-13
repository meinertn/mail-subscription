<?php
use Phppot\Subscription;
use Phppot\SupportService;

/**
 * For confirmation action.
 * 1. Get the secure has from url
 * 2. validate it against url
 * 3. update the subscription status in database accordingly.
 */
session_start();

// to ensure the request via POST
require_once __DIR__ . '/lib/SupportService.php';
$supportService = new SupportService();

// to Debug set as true
$supportService->setDebug(true);

$subscriptionKey = $_GET['q'];

require_once __DIR__ . '/Model/Subscription.php';
$subscription = new Subscription();
$result = $subscription->getMember($subscriptionKey, 0);

if (count($result) > 0) {
    // member found, go ahead and update status
    $subscription->updateStatus($subscriptionKey, 1);
    $message = $result[0]['member_name'] . ', your subscription is confirmed.';
    $messageType = 'success';
} else {
    // securiy precaution: do not reveal any information here
    // play subtle with the reported message
    $message = 'Invalid URL!';
    $messageType = 'error';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport"
	content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Vincy">
<link rel="stylesheet" type="text/css"
	href="assets/css/phppot-style.css">
<title>Double Opt-In Subscription Confirmation</title>
</head>
<body>
	<div class="phppot-container">
		<h1>Double Opt-in Subscription Confirmation</h1>
		<div class="phppot-row">
			<div id="phppot-message" class="<?php echo $messageType; ?>"><?php echo $message;?></div>
		</div>
	</div>
</body>
</body>
</html>