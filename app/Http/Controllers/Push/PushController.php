<?php

namespace App\Http\Controllers\Push;

use App\Http\Requests\Push\PushRequest;
use App\Http\Requests\Push\PushRegisterRequest;
use App\Http\Requests\Push\PushtUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\Push;
use App\Utils\Messages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushController extends Controller
{
    /**
     * Fetch list push
     */
    public function __invoke(PushRequest $request)
    {
        $validated = $request->validated();
        try {
            // If per_page is null set default data = 15
            $per_page = isset($validated['per_page']) ? $validated['per_page'] : 15;
            // If page is null set default data = 1
            $page = isset($validated['page']) ? $validated['page'] : 1;
            $pushs = Push::paginate($per_page, ['*'], 'page', $page);
            return response()->json($pushs);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => Messages::MSG_0018], 500);
        }
    }
    
    /**
     * Get Push detail by id
     */
    public function getPushDetail(Push $push){
        return response()->json($push);
    }

    /**
     * Register Contract
     * @param  App\Http\Requests\Push\PushRegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPush(PushRegisterRequest $request)
    {
        $validated = $request->validated();
        try {
            $push = Push::insertGetId([
                'mb_no' => Auth::user()->mb_no,
                'menu_no' => $validated['menu_no'],
                'push_title' => $validated['push_title'],
                'push_content' => $validated['push_content'],
                'push_time' => $validated['push_time'],
                'push_must_yn' => $validated['push_must_yn'],
                'push_use_yn' => $validated['push_use_yn']
            ]);

            return response()->json([
                'message' => Messages::MSG_0007,
                'push' => $push,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => Messages::MSG_0001], 500);
        }
    }

    /**
     * Update Push
     * @param  App\Http\Requests\Push\PushtUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePush(PushtUpdateRequest $request, Push $push)
    {
        $validated = $request->validated();
        try {
            $update = [
                'menu_no' => $validated['menu_no'],
                'push_title' => $validated['push_title'],
                'push_content' => $validated['push_content'],
                'push_time' => $validated['push_time'],
                'push_must_yn' => $validated['push_must_yn'],
                'push_use_yn' => $validated['push_use_yn'],
            ];

            $push = Push::where(['push_no' => $push->push_no])
                ->update($update);
            return response()->json([
                'message' => Messages::MSG_0007,
                'push' => $push,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => Messages::MSG_0002], 500);
        }
    }
}
