<?php namespace App\Base;

class Validators
{
    public function requireValidate($filter, $val, $allowedEmpties = [])
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
                return $field;
            }
        }
        return true;
    }
}