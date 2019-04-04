<?php

/**
 * @const   int
 */
const VALID_CNPJ_LENGTH = 14;

/**
 * @see validate()
 */
const FIRST_DIGIT_POSITION = 12;

/**
 * @see validate()
 */
const SECOND_DIGIT_POSITION = 13;

class CnpjValidator 
{
    public static function format($cnpj){
        $cnpj = preg_replace("/[^0-9]/","", $cnpj);
 
        if(strlen($cnpj) > 0) {
            $cnpj = str_pad($cnpj, VALID_CNPJ_LENGTH, '0', STR_PAD_LEFT);
            $cnpj = substr($cnpj,0,2) . "." . substr($cnpj,2,3) . "." . substr($cnpj,5,3) . "/" . substr($cnpj,8,4) . "-" . substr($cnpj,12,2);
        }
        return $cnpj;
    }

    public static function validate ($cnpj)
    {
        $cnpj_numbers = self::clean($cnpj);

        if ( !self::hasValidPattern( $cnpj ) ) {
            return false;
        }

        if( strlen( $cnpj_numbers ) !== VALID_CNPJ_LENGTH ) {
            return false;
        }

        $dg1 = self::calculateDigit( $cnpj_numbers, FIRST_DIGIT_POSITION );

        if ( $dg1 != $cnpj_numbers{FIRST_DIGIT_POSITION} ) {
            return false;
        }

        // lazy calculated
        $dg2 = self::calculateDigit( $cnpj_numbers, SECOND_DIGIT_POSITION );

        if ( $dg2 != $cnpj_numbers{SECOND_DIGIT_POSITION} ) {
            return false;
        }

        return true;
    }

    /**
     * Extract only the digits
     *
     * @param   string/int  $cnpj
     * @return string/int
     */
    private static function clean($cnpj)
    {
        return preg_replace("/[^0-9]/", "", $cnpj);
    }

    /**
     * Check if a given CNPJ has a valid pattern
     *
     * @param $cnpj
     * @see validate()
     *
     * @return bool
     */
    private static function hasValidPattern($cnpj)
    {
        $mask = "/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$/";

        if( ! preg_match($mask, $cnpj) && !preg_match( "/^[0-9]{14}$/", $cnpj ) ) {
            return false;
        }

        return true;
    }

    /**
     * Caculate the digi by position
     *
     * @param   string  $cnpj
     * @param   int     $str_length
     * @see     validate()
     *
     * @return  int
     */
    private static function calculateDigit($cnpj, $str_length)
    {
        $sum     = 0;
        $pos     = $str_length - 7;
        $numbers = substr($cnpj,0, $str_length);

        for ($i = $str_length; $i >= 1; $i--) {
            $sum += $numbers{$str_length - $i} * $pos--;
            if ($pos < 2) $pos = 9;
        }

        return ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);
    }
}