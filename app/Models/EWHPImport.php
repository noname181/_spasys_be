<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EWHPImport extends Model
{
    use HasFactory;

    protected $table = "EWHP_import";

    protected $primaryKey = 'i_no';

    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'i_report_type',
        'i_date',
        'i_time',
        'i_product_number',
        'i_import_type',
        'i_hbl',
        'i_mbl',
        'i_report_number',
        'i_classification',
        'i_packaging_type',
        'i_amount',
        'i_weight',
        'i_unit',
        'i_business_code',
    ];

    
}
