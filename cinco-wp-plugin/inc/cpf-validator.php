<?php
/**
 * @var int VALID_CPF_LENGTH
 */
const VALID_CPF_LENGTH = 11;

class CpfValidator
{
    public static function format($cpf){
        $cpf = preg_replace("/[^0-9]/","",$cpf);

        if(strlen($cpf) > 0){
            $cpf = str_pad($cpf, VALID_CPF_LENGTH, '0', STR_PAD_LEFT);
            $cpf = substr($cpf,0,3) . "." . substr($cpf,3,3) . "." . substr($cpf,6,3) . "-" . substr($cpf,9,2);
        }
        return $cpf;
    }

    /**
     * Validate a given Brazilian CPF number
     *
     * @param   $cpf
     * @return  bool
     */
    public static function validate($cpf)
    {
        $raw_cpf    = $cpf;
        $cpf        = self::clean($cpf);
        $has_mask   = preg_match("/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/", $raw_cpf);

        if (!$has_mask && !is_numeric($raw_cpf)) {
            return false;
        }

        if (!self::isLengthValid($cpf)) {
            return false;
        }

        elseif (self::isDummyValue($cpf)) {
            return false;
        }

        return self::validateDigits($cpf);
    }

    /**
     * Check if a given CPF value has a valid length
     *
     * @see VALID_CPF_LENGTH
     * @param   $cpf
     * @return  bool
     */
    public static function isLengthValid ($cpf) {

        return (strlen($cpf) === VALID_CPF_LENGTH);
    }

    /**
     * Extract only the number of a given CPF
     *
     * @param   $cpf
     * @return  mixed
     */
    public static function clean($cpf)
    {
        return preg_replace("/[^0-9]/", "", $cpf);
    }

    /**
     * Check if a given CPF value is a dummy sequence
     *
     * @param   $cpf
     * @return  bool
     */
    public static function isDummyValue($cpf)
    {
        return (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        );
    }

    /**
     * @param $cpf
     * @return bool
     */
    private static function validateDigits ($cpf)
    {
        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }

        return true;
    }
}