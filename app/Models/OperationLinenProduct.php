<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OperationLinenProduct extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public $table = "operations_linen_products";

    public function linenProduct()
    {
        return $this->belongsTo(LinenProduct::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    // Need to full join with operations table first to use this function
    public function operationCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array['linen_case'] = (function ($jobVar) {
            foreach (OperationLinenCase::$list as $linenCase) {
                if ($linenCase['var'] == $jobVar) {
                    return $linenCase;
                }
            }
            return $jobVar;
        })($array['linen_case']);

        return $array;
    }
}
