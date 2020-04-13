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

class SupportService
{

    /**
     * Short circuit type function to stop the process flow on validation failure.
     */
    public function validateAjaxRequest()
    {
        // to check if its an ajax request, exit if not
        $http_request = $_SERVER['HTTP_X_REQUESTED_WITH'];
        if (! isset($http_request) && strtolower($http_request) != 'xmlhttprequest') {
            $output = $this->createJsonInstance('Not a valid AJAX request!');
            $this->endAction($output);
        }
    }

    /**
     * Last point in the AJAX work flow.
     * Clearing tokens, handles and resource cleanup can be done here.
     *
     * @param string $output
     * @param boolean $clearToken
     */
    public function endAction($output)
    {
        die($output);
    }

    public function setDebug($mode)
    {
        if ($mode == true) {
            ini_set('display_errors', 1);
            set_error_handler(function ($severity, $message, $file, $line) {
                if (error_reporting() & $severity) {
                    throw new \ErrorException($message, 0, $severity, $file, $line);
                }
            });
        }
    }

    /**
     * encodes a message string into a json object
     *
     * @param string $message
     * @param string $type
     * @return \JsonSerializable encoded json object
     */
    public function createJsonInstance($message, $type = 'error')
    {
        $messageArray = array(
            'type' => $type,
            'text' => $message
        );
        $jsonObj = json_encode($messageArray);
        return $jsonObj;
    }

    public function getJsonValue($json, $key)
    {
        $jsonArray = json_decode($json, true);
        return $jsonArray[$key];
    }

    /**
     * If you are using PHP, this is the best possible secure hash
     * do not try to implement somthing on your own
     *
     * @param string $text
     * @return string
     */
    public function getSecureHash($text)
    {
        $hashedText = password_hash($text, PASSWORD_DEFAULT);
        return $hashedText;
    }

    /**
     * generates a random token of the length passed
     *
     * @param int $length
     * @return string
     */
    public function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            return $min; // not so random...
        }
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    /**
     * makes the passed string url safe and return encoded url
     *
     * @param string $str
     * @return string
     */
    public function cleanUrl($str, $isEncode = 'true')
    {
        $delimiter = "-";
        $str = str_replace(' ', $delimiter, $str); // Replaces all spaces with hyphens.
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // allows only alphanumeric and -
        $str = trim($str, $delimiter); // remove delimiter from both ends
        $regexConseqChars = '/' . $delimiter . $delimiter . '+/';
        $str = preg_replace($regexConseqChars, $delimiter, $str); // remove consequtive delimiter
        $str = mb_strtolower($str, 'UTF-8'); // convert to all lower
        if ($isEncode) {
            $str = urldecode($str); // encode to url
        }
        return $str;
    }

    /**
     * to mitigate XSS attack
     */
    public function xssafe($data, $encoding = 'UTF-8')
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    /**
     * convenient method to print XSS mitigated text
     *
     * @param string $data
     */
    public function xecho($data)
    {
        echo $this->xssafe($data);
    }
}
