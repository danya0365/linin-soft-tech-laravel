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
}
