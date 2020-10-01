<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Http\Transformers\EmployeeTransformer;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'name' => 'required|string',
            'profile_image_url' => 'sometimes|required|string',
            'role_id' => 'sometimes|required|exists:roles,id',
            'department_id' => 'sometimes|required|exists:departments,id',
            'employee_type_id' => 'sometimes|required|exists:employee_types,id',
            'active' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }

        DB::beginTransaction();
        try {
            $employee = EmployeeTransformer::toInstance($request->all());
            $employee->save();
            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return (new EmployeeResource($employee))
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => "employee created"
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return $this->respondNotImplemented();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'profile_image_url' => 'sometimes|required|string',
            'role_id' => 'sometimes|required|exists:roles,id',
            'department_id' => 'sometimes|required|exists:departments,id',
            'employee_type_id' => 'sometimes|required|exists:employee_types,id',
            'active' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }

        DB::beginTransaction();
        try {
            $employee = EmployeeTransformer::toInstance($request->all(), $employee);
            $employee->save();
            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return (new EmployeeResource($employee))
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => "employee updated"
                ]
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        try {
            $employee->delete();
            $employee->active = false;
            $employee->save();
            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return $this->respondSuccess('', $employee->name . ' has been deleted');
    }
}
