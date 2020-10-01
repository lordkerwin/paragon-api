<?php

namespace App\Http\Transformers;

use App\Models\PayRate;

class PayRateTransformer
{
    /**
     * Create or update a pay_rate instance
     *
     * @param Array $input Array of key value pairs
     * @param PayRate $pay_rate instance (can be null)
     * @return PayRate
     */
    public static function toInstance(array $input, $pay_rate = null)
    {
        if (empty($pay_rate)) {
            $pay_rate = new PayRate();
        }

        foreach ($input as $key => $value) {
            switch ($key) {
                case 'rate':
                    $pay_rate->rate = $value;
                    break;
            }
        }

        return $pay_rate;
    }
}
