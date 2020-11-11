<?php

namespace App\Http\Transformers;


use App\Models\Shift;

class ShiftTransformer
{
    /**
     * Create or update a pay_rate instance
     *
     * @param  array  $input  Array of key value pairs
     * @param  Shift|null  $shift  instance (can be null)
     * @return Shift
     */
    public static function toInstance(array $input, $shift = null)
    {
        if (empty($shift)) {
            $shift = new Shift();
        }

        foreach ($input as $key => $value) {
            switch ($key) {
                case 'employee_id':
                    $shift->employee_id = $value;
                    break;
                case 'start':
                    $shift->start = $value;
                    break;
                case 'end':
                    $shift->end = $value;
                    break;
                case 'pay_rate':
                    $shift->pay_rate = $value;
                    break;
                case 'value':
                    $shift->value = $value;
                    break;
                case 'shift_status_id':
                    $shift->shift_status_id = $value;
                    break;
                case 'processed':
                    $shift->processed = $value;
                    break;
                case 'payroll_locked':
                    $shift->payroll_locked = $value;
                    break;
            }
        }

        return $shift;
    }
}
