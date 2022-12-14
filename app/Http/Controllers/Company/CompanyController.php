<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests\Company\CompanyRegisterController\InvokeRequest;
use App\Http\Requests\Company\CompanyRequest;
use App\Http\Requests\Company\CompanySearchRequest;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\AdjustmentGroup;
use App\Models\CoAddress;
use App\Models\ForwarderInfo;
use App\Models\CustomsInfo;
use App\Utils\Messages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Register company
     * @param  \App\Http\Requests\Company\CompanyRegisterController\InvokeRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(InvokeRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();

            if(Auth::user()->mb_type == 'spasys'){
                $co_type = 'agency';
            }else if(Auth::user()->mb_type == 'agency'){
                $co_type = 'shop';
            }else {
                $co_type = 'spasys';
            }
            $co_no = Company::insertGetId([
                'mb_no' => Auth::user()->mb_no,
                'co_name' => $validated['co_name'],
                'co_address' => $validated['co_address'],
                'co_address_detail' => $validated['co_address_detail'],
                'co_country' => $validated['co_country'],
                'co_service' => $validated['co_service'],
                'co_owner' => $validated['co_owner'],
                'co_license' => $validated['co_license'],
                'co_homepage' => $validated['co_homepage'],
                'co_email' => $validated['co_email'],
                'co_etc' => $validated['co_etc'],
                'co_type' => $co_type
            ]);

            $company = Company::where('co_no', $co_no)->first();

            // $ca_no = COAddress::insertGetId([
            //     'co_no' => $validated['co_no'],
            //     'mb_no' => Auth::user()->mb_no,
            //     'ca_address' => $validated['co_address'],
            //     'ca_address_detail' => $validated['co_address_detail']
            // ]);

            DB::commit();
            return response()->json([
                'message' => Messages::MSG_0007,
                'company' => $company,
                // 'ca_no' => $ca_no,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return $e;
            return response()->json(['message' => Messages::MSG_0001], 500);
        }
    }

    public function getCompanies(CompanySearchRequest $request)
    {
        try {
            $validated = $request->validated();

            // If per_page is null set default data = 15
            $per_page = isset($validated['per_page']) ? $validated['per_page'] : 15;
            // If page is null set default data = 1
            $page = isset($validated['page']) ? $validated['page'] : 1;
            $companies = Company::with('contract')->orderBy('co_no', 'DESC');

            if (isset($validated['from_date'])) {
                $companies->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($validated['from_date'])));
            }

            if (isset($validated['to_date'])) {
                $companies->where('updated_at', '<=', date('Y-m-d 23:59:00', strtotime($validated['to_date'])));
            }

            if (isset($validated['co_name'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_name)'), 'like', '%' . strtolower($validated['co_name']) . '%');
                });
            }

            if (isset($validated['co_service'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_service)'), 'like', '%' . strtolower($validated['co_service']) . '%');
                });
            }
            
            $companies = $companies->paginate($per_page, ['*'], 'page', $page);
            
            return response()->json($companies);
        } catch (\Exception $e) {
            Log::error($e);
            return $e;
            return response()->json(['message' => Messages::MSG_0018], 500);
        }
    }

    public function getCompany($co_no)
    {
        try {
            $company = Company::select([
                'company.co_no',
                'company.mb_no',
                'company.co_name',
                'company.co_address',
                'company.co_address_detail',
                'company.co_country',
                'company.co_service',
                'company.co_license',
                'company.co_owner',
                'company.co_homepage',
                'company.co_email',
                'company.co_etc',
                // 'co_address.ca_address as co_address',
                // 'co_address.ca_address_detail as co_address_detail',
            // ])->join('co_address', 'co_address.co_no', 'company.co_no')
            ])->where('company.co_no', $co_no)
                // ->where('co_address.co_no', $co_no)
                ->first();

            $adjustment_groups = AdjustmentGroup::select(['ag_no', 'co_no', 'ag_name', 'ag_manager', 'ag_hp', 'ag_email'])->where('co_no', $co_no)->get();
            $co_address = CoAddress::select(['ca_no', 'co_no', 'ca_name', 'ca_manager', 'ca_hp', 'ca_address', 'ca_address_detail'])->where('co_no', $co_no)->get();
            $forwarder_info = ForwarderInfo::select(['fi_no', 'co_no', 'fi_name', 'fi_manager', 'fi_hp', 'fi_address', 'fi_address_detail'])->where('co_no', $co_no)->get();
            $customs_info = CustomsInfo::select(['ci_no', 'co_no', 'ci_name', 'ci_manager', 'ci_hp', 'ci_address', 'ci_address_detail'])->where('co_no', $co_no)->get();

            return response()->json([
                'message' => Messages::MSG_0007,
                'company' => $company,
                'adjustment_groups' => $adjustment_groups,
                'co_address' => $co_address,
                'forwarder_info' => $forwarder_info,
                'customs_info' => $customs_info,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return $e;
            return response()->json(['message' => Messages::MSG_0020], 500);
        }
    }

    /**
     * Register company
     * @param  \App\Http\Requests\Company\CompanyRegisterController\InvokeRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCompany(InvokeRequest $request, Company $company)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();

            $comp = Company::where('co_no', $company->co_no)
                ->where('mb_no', Auth::user()->mb_no)
                ->update([
                    'co_name' => $validated['co_name'],
                    'co_address' => $validated['co_address'],
                    'co_address_detail' => $validated['co_address_detail'],
                    'co_country' => $validated['co_country'],
                    'co_service' => $validated['co_service'],
                    'co_owner' => $validated['co_owner'],
                    'co_license' => $validated['co_license'],
                    'co_homepage' => $validated['co_homepage'],
                    'co_email' => $validated['co_email'],
                    'co_etc' => $validated['co_etc'],
                ]);


            // COAddress::where('co_no', $company->co_no)
            //     ->where('mb_no', Auth::user()->mb_no)
            //     ->update([
            //         'ca_address' => $validated['co_address'],
            //         'ca_address_detail' => $validated['co_address_detail']
            //     ]);

            DB::commit();
            return response()->json([
                'message' => Messages::MSG_0007,
                'company' => $comp,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return response()->json(['message' => Messages::MSG_0002], 500);
        }
    }

    public function  getAgencyCompanies (CompanySearchRequest $request){
        try {
            $validated = $request->validated();

            // If per_page is null set default data = 15
            $per_page = isset($validated['per_page']) ? $validated['per_page'] : 15;
            // If page is null set default data = 1
            $page = isset($validated['page']) ? $validated['page'] : 1;
            $companies = Company::with('contract')->where('mb_no', Auth::user()->mb_no)->orderBy('co_no', 'DESC');

            if (isset($validated['from_date'])) {
                $companies->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($validated['from_date'])));
            }

            if (isset($validated['to_date'])) {
                $companies->where('updated_at', '<=', date('Y-m-d 23:59:00', strtotime($validated['to_date'])));
            }

            if (isset($validated['co_name'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_name)'), 'like', '%' . strtolower($validated['co_name']) . '%');
                });
            }

            if (isset($validated['co_service'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_service)'), 'like', '%' . strtolower($validated['co_service']) . '%');
                });
            }
            
            $companies = $companies->paginate($per_page, ['*'], 'page', $page);
            
            return response()->json($companies);
        } catch (\Exception $e) {
            Log::error($e);
            return $e;
            return response()->json(['message' => Messages::MSG_0018], 500);
        }
    }

    public function  getShopCompanies (CompanySearchRequest $request){
        try {
            $validated = $request->validated();

            // If per_page is null set default data = 15
            $per_page = isset($validated['per_page']) ? $validated['per_page'] : 15;
            // If page is null set default data = 1
            $page = isset($validated['page']) ? $validated['page'] : 1;
            $companies = Company::with('contract')->where('mb_no', Auth::user()->mb_no)->orderBy('co_no', 'DESC');

            if (isset($validated['from_date'])) {
                $companies->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($validated['from_date'])));
            }

            if (isset($validated['to_date'])) {
                $companies->where('updated_at', '<=', date('Y-m-d 23:59:00', strtotime($validated['to_date'])));
            }

            if (isset($validated['co_name'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_name)'), 'like', '%' . strtolower($validated['co_name']) . '%');
                });
            }

            if (isset($validated['co_service'])) {
                $companies->where(function($query) use ($validated) {
                    $query->where(DB::raw('lower(co_service)'), 'like', '%' . strtolower($validated['co_service']) . '%');
                });
            }
            
            $companies = $companies->paginate($per_page, ['*'], 'page', $page);
            
            return response()->json($companies);
        } catch (\Exception $e) {
            Log::error($e);
            return $e;
            return response()->json(['message' => Messages::MSG_0018], 500);
        }
    }
}
