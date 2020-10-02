<?php

namespace App\Http\Transformers;

use App\Models\Employee;
use App\Models\EmployeePayRate;

class EmployeePayRateTransformer
{

    public static function toInstance(array $input, $employee_pay_rate = null)
    {
        if (empty($employee_pay_rate)) {
            $employee_pay_rate = new EmployeePayRate();
        }

        foreach ($input as $key => $value) {
            switch ($key) {
                case 'employee_id':
                    $employee_pay_rate->employee_id = $value;
                    break;
                case 'pay_rate_id':
                    $employee_pay_rate->pay_rate_id = $value;
                    break;
                case 'from':
                    $employee_pay_rate->from = $value;
                    break;
                case 'to':
                    $employee_pay_rate->to = $value;
                    break;
            }
        }

        return $employee_pay_rate;
    }
}
