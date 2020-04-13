<?php
use Phppot\Subscription;
use Phppot\SupportService;

/**
 * AJAX end point for subscribe action.
 * 1. validate the user input
 * 2. store the details in database
 * 3. send email with link that has secure hash for opt-in confirmation
 */
session_start();

// to ensure the request via POST
if ($_POST) {
    require_once __DIR__ . './../lib/SupportService.php';
    $supportService = new SupportService();

    // to Debug set as true
    $supportService->setDebug(false);

    // to check if its an ajax request, exit if not
    $supportService->validateAjaxRequest();

    require_once __DIR__ . './../Model/Subscription.php';
    $subscription = new Subscription();

    // get user input and sanitize
    if (isset($_POST["pp-email"])) {
        $userEmail = trim($_POST["pp-email"]);
        $userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
        $subscription->setEmail($userEmail);
    } else {
        // server side fallback validation to check if email is empty
        $output = $supportService->createJsonInstance('Email is empty!');
        $supportService->endAction($output);
    }

    $memberName = "";
    if (isset($_POST["pp-name"])) {
        $memberName = filter_var($_POST["pp-name"], FILTER_SANITIZE_STRING);
    }
    $subscription->setMemberName($memberName);

    // 1. get a 12 char length random string token
    $token = $supportService->getToken(12);

    // 2. make that random token to a secure hash
    $secureToken = $supportService->getSecureHash($token);

    // 3. convert that secure hash to a url string
    $urlSecureToken = $supportService->cleanUrl($secureToken);
    $subscription->setSubsriptionKey($urlSecureToken);
    $subscription->setSubsciptionSatus(0);

    $currentTime = date("Y-m-d H:i:s");
    $subscription->setCreateAt($currentTime);
    $result = $subscription->insert();

    // check if the insert is success
    // if success send email else send message to user
    $messageType = $supportService->getJsonValue($result, 'type');
    if ('error' != $messageType) {
        $result = $subscription->sendConfirmationMessage($userEmail, $urlSecureToken);
    }
    $supportService->endAction($result);
}