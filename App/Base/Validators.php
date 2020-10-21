<?php namespace App\Base;

class Validators
{
    public static function requireValidate($filter, $val, $allowedEmpties = [])
    {
        $filter = (array)$filter;

        foreach ($filter as $key => $field) {

            if ($key == 'msg') {
                continue;
            }

            if (key_exists($field, $val) && (
                    $val[$field] || is_int($val[$field]) || is_string($val[$field]) && strlen(trim($val[$field])) ||
                    (isset($allowedEmpties[$field]) && in_array($val[$field], $allowedEmpties[$field], true))
                ))
            {
                continue;
            } else {
                if (isset($filter['msg'])) {
                    return isset($filter['msg'][$field]) ?  $filter['msg'][$field] : $field;
                }
                return $field;
            }
        }
        return true;
    }
}