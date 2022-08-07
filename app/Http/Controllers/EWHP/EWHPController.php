<?php

namespace App\Http\Controllers\EWHP;

use DateTime;
use App\Models\EWHPImport;
use App\Models\EWHPImportExpected;
use App\Models\EWHPExport;
use App\Utils\Messages;
use App\Utils\CommonFunc;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;



class EWHPController extends Controller
{
    /**
     * Fetch data
     * @param  \App\Http\Requests\Notice\NoticeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import_expected(Request $request)
    {
        try {
            DB::beginTransaction();
            $ewhp = EWHPImportExpected::insertGetId([
                'ie_date' => $request->ie_date,
                'ie_nature' => $request->ie_nature,
                'ie_product_number' => $request->ie_product_number,
                'ie_amount' => $request->ie_amount,
                'ie_weight' => $request->ie_weight,
                'ie_unit' => $request->ie_unit,
                'ie_mbl' => $request->ie_mbl,
                'ie_hbl' => $request->ie_hbl,
                'ie_eng_name' => $request->ie_eng_name,
                'ie_shipper' => $request->ie_shipper,
                'ie_business_code' => $request->ie_business_code,
                'ie_owner_name' => $request->ie_owner_name,
            ]);

            DB::commit();

            return response()->json([
                'message' => Messages::MSG_0007,
                'ie_no' => $ewhp
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['message' => 'Insert failed'], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            $ewhp = EWHPImport::insertGetId([
                'i_report_type' => $request->i_report_type,
                'i_date' => $request->i_date,
                'i_time' => $request->i_time,
                'i_product_number' => $request->i_product_number,
                'i_import_type' => $request->i_import_type,
                'i_mbl' => $request->i_mbl,
                'i_hbl' => $request->i_hbl,
                'i_report_number' => $request->i_report_number,
                'i_classification' => $request->i_classification,
                'i_packaging_type' => $request->i_packaging_type,
                'i_amount' => $request->i_amount,
                'i_weight' => $request->i_weight,
                'i_unit' => $request->i_unit,
                'i_business_code' => $request->i_business_code,
            ]);
            DB::commit();
            return response()->json([
                'message' => Messages::MSG_0007,
                'i_no' => $ewhp
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['message' => 'Insert failed'], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            DB::beginTransaction();
            $ewhp = EWHPExport::insertGetId([
                'e_date' => $request->e_date,
                'e_time' => $request->e_time,
                'e_product_number' => $request->e_product_number,
                'e_mbl' => $request->e_mbl,
                'e_hbl' => $request->e_hbl,
                'e_classification' => $request->e_classification,
                'e_packaging_type' => $request->e_packaging_type,
                'e_amount' => $request->e_amount,
                'e_weight' => $request->e_weight,
                'e_export_type' => $request->e_export_type,
                'e_delivery_command' => $request->e_delivery_command,
                'e_exchange_rate' => $request->e_exchange_rate,
            ]);
            DB::commit();
            return response()->json([
                'message' => Messages::MSG_0007,
                'e_no' => $ewhp
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['message' => 'Insert failed'], 500);
        }
    }

}
