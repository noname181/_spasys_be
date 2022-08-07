<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EWHPExport extends Model
{
    use HasFactory;

    protected $table = "EWHP_export";

    protected $primaryKey = 'e_no';

    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'e_date',
        'e_time',
        'e_product_number',
        'e_mbl',
        'e_hbl',
        'e_classification',
        'e_packaging_type',
        'e_amount',
        'e_weight',
        'e_export_type',
        'e_delivery_command',
        'e_exchange_rate',
    ];

    
}
