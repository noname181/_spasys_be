<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EWHPImportExpected extends Model
{
    use HasFactory;

    protected $table = "EWHP_import_expect";

    protected $primaryKey = 'ie_no';

    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'ie_date',
        'ie_nature',
        'ie_product_number',
        'ie_amount',
        'ie_weight',
        'ie_unit',
        'ie_mbl',
        'ie_hbl',
        'ie_eng_name',
        'ie_shipper',
        'ie_business_code',
        'ie_owner_name',
    ];

    
}
