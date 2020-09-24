<?php

namespace App\Http\Controllers;

use App\Http\Resources\PayRateResource;
use App\Http\Transformers\PayRateTransformer;
use App\Models\PayRate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayRateController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PayRateResource::collection(
            PayRate::orderBy('rate', 'asc')->paginate($request->input('paginate') ?? 15)
        )
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => 'payrates loaded'
                ]
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->respondNotImplemented();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|unique:pay_rates|between:0,50'
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }

        DB::beginTransaction();
        try {
            $pay_rate = PayRateTransformer::toInstance($request->all());
            $pay_rate->save();
            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return (new PayRateResource($pay_rate))
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => "payrate created"
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayRate  $payRate
     * @return \Illuminate\Http\Response
     */
    public function show(PayRate $payRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayRate  $payRate
     * @return \Illuminate\Http\Response
     */
    public function edit(PayRate $payRate)
    {
        return $this->respondNotImplemented();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PayRate  $payRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PayRate $payRate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayRate  $payRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayRate $payRate)
    {
        return $this->respondNotImplemented();
    }
}
