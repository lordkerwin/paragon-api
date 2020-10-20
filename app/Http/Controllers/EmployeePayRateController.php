<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeePayRateResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Transformers\EmployeePayRateTransformer;
use App\Http\Transformers\EmployeeTransformer;
use App\Models\Employee;
use App\Models\EmployeePayRate;
use App\Models\PayRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class EmployeePayRateController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        return $this->respondNotImplemented();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return EmployeePayRateResource|EmployeeResource|JsonResponse
     * @throws Throwable
     */
    public function store(Request $request)
    {
        //        dd($request);
        $validator = Validator::make($request->all(), [
            'employee_id' => 'sometimes|required|exists:employees,id',
            'pay_rate_id' => 'sometimes|required|exists:pay_rates,id',
            'from' => 'sometimes|date',
            'to' => 'sometimes|nullable|after_or_equal:from'
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }


        // check if an employee_pay_rate record already exists with the same pay_rate and from time
        $employee_pay_rate = EmployeePayRate::where('employee_id', $request->input('employee_id'))
            ->where('pay_rate_id', $request->input('pay_rate_id'))
            ->where('from', $request->input('from'))
            ->first();

        if ($employee_pay_rate) {
            // we have found a matching pivot record so we need to stop the user creating another
            // pay rate with the same 'from' time as a user can only have one.
            return $this->respondError($employee_pay_rate, "There seems to already be a record for this user with a matching start time", 422);
        }


        // find an employee
        try {
            $employee = Employee::findOrFail($request->input('employee_id'));
        } catch (Exception $ex) {
            return $this->respondError(null, "failed to find employee", 404);
        }

        // find a pay rate
        try {
            $pay_rate = PayRate::findOrFail($request->input('pay_rate_id'));
        } catch (Exception $ex) {
            return $this->respondError(null, "failed to find pay rate with ID " . $request->input('pay_rate_id'), 404);
        }

        DB::beginTransaction();
        try {
            // TODO: Write a check that stops an employee from having overlapping pay_rates from/to
            // example: if an employee is on rate_id 1 from Sep 01 2019 and 'to' is null,
            // if we create a new rate from Jan 01, 2020 to be rate 2, we need to set the 'to' field of
            // the rate 1 pivot to be the start of rate 2.

            $p = EmployeePayRate::where('employee_id', $request->input('employee_id'))
                ->where('to', null)
                ->first();
            $p->to = $request->input('from');
            $p->save();

            // attach the new pay rate
            $employee->payRates()->attach($pay_rate->id, [
                'from' => $request->input('from'),
                'to' => $request->input('to')
            ]);

            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return $this->respondSuccess(null, 'Employee Pay Rate attached');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function edit()
    {
        return $this->respondNotImplemented();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
