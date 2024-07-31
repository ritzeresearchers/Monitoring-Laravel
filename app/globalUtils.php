<?php

if (!function_exists('generateRandomString')) {
    /**
     * @param int $length
     * @return string
     */
    function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('generateRandomNumber')) {
    /**
     * @param int $digits
     * @return string
     */
    function generateRandomNumber(int $digits = 6): string
    {
        return str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    }
}


if (!function_exists('isValidMobileNumber')) {
    /**
     * @param $phone
     * @return false|int
     */
    function isValidMobileNumber($phone)
    {
        return preg_match('/^[a-z0-9_]+$/i', $phone);
    }
}

if (!function_exists('isValidEmail')) {
    /**
     * @param $email
     * @return mixed
     */
    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
