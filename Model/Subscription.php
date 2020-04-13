<?php
/**
 * Copyright (C) 2019 Phppot
 * 
 * Distributed under MIT license with an exception that, 
 * you don’t have to include the full MIT License in your code.
 * In essense, you can use it on commercial software, modify and distribute free.
 * Though not mandatory, you are requested to attribute this URL in your code or website.
 */
namespace Phppot;

use Phppot\DataSource;

class Subscription
{

    private $ds;

    private $memberName;

    private $email;

    private $subsriptionKey;

    private $subsciptionSatus;

    private $createAt;

    private $supportService;

    function __construct()
    {
        require_once __DIR__ . './../lib/DataSource.php';
        $this->ds = new DataSource();

        require_once __DIR__ . './../lib/SupportService.php';
        $this->supportService = new SupportService();
    }

    public function getMemberName()
    {
        return $this->memberName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSubsriptionKey()
    {
        return $this->subsriptionKey;
    }

    public function getSubsciptionSatus()
    {
        return $this->subsciptionSatus;
    }

    public function getCreateAt()
    {
        return $this->createAt;
    }

    public function setMemberName($memberName)
    {
        $this->memberName = $memberName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSubsriptionKey($subsriptionKey)
    {
        $this->subsriptionKey = $subsriptionKey;
    }

    public function setSubsciptionSatus($subsciptionSatus)
    {
        $this->subsciptionSatus = $subsciptionSatus;
    }

    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    }

    /**
     * to get the member record based on the subscription_key
     *
     * @param string $subscriptionKey
     * @return array result record
     */
    public function getMember($subscriptionKey, $subscriptionStatus)
    {
        $query = 'SELECT * FROM tbl_subscription where subscription_key = ? and subscription_status = ?';
        $paramType = 'si';
        $paramValue = array(
            $subscriptionKey,
            $subscriptionStatus
        );
        $result = $this->ds->select($query, $paramType, $paramValue);
        return $result;
    }

    public function insert()
    {
        $query = 'INSERT INTO tbl_subscription (member_name, email, subscription_key, subscription_status, create_at) VALUES (?, ?, ?, ?, ?)';
        $paramType = 'sssis';
        $paramValue = array(
            $this->memberName,
            $this->email,
            $this->subsriptionKey,
            $this->subsciptionSatus,
            $this->createAt
        );
        $insertStatus = $this->ds->insert($query, $paramType, $paramValue);
        return $insertStatus;
    }

    public function updateStatus($subscriptionKey, $subscriptionStatus)
    {
        $query = 'UPDATE tbl_subscription SET subscription_status = ? WHERE subscription_key = ?';
        $paramType = 'is';
        $paramValue = array(
            $subscriptionStatus,
            $subscriptionKey
        );
        $this->ds->execute($query, $paramType, $paramValue);
    }

    /**
     * sends confirmation email, to keep it simple, I am just using the PHP's mail
     * I reccommend serious users to change it to PHPMailer and set
     * appropriate headers
     */
    public function sendConfirmationMessage($mailTo, $urlSecureToken)
    {
        // following is the opt-in url that will be sent in email to
        // the subscriber. Replace example.com with your server
        $confirmOptInUrl = 'http://example.com/confirm.php?q=' . $urlSecureToken;
        $message = '<p>Howdy!</p>
        <p>This is an automated message sent for subscription service.
You must confirm your request to subscribe to example.com site.</p>
        <p>Website Name: example</p>
        <p>Website URL: http://example.com</p>
        <p>Click the following link to confirm: ' . $confirmOptInUrl . '</p>';

        $isSent = mail($mailTo, 'Confirm your subscription', $message);

        if ($isSent) {
            $message = "An email is sent to you. You should confirm the subscription by clicking the link in the email.";
            $result = $this->supportService->createJsonInstance($message, 'message');
        } else {
            $result = $this->supportService->createJsonInstance('Error in sending confirmation email.', 'error');
        }
        return $result;
    }
}