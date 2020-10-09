<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Http\Transformers\EmployeeTransformer;
use App\Models\Employee;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Json;
use Throwable;

class EmployeeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return void
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
     * @param  Request  $request
     * @return EmployeeResource|JsonResponse
     * @throws Throwable
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
     * @param  Employee  $employee
     * @return Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Employee  $employee
     * @return JsonResponse
     */
    public function edit(Employee $employee)
    {
        return $this->respondNotImplemented();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Employee  $employee
     * @return JsonResponse
     * @throws Throwable
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
     * @param  Employee  $employee
     * @return JsonResponse
     * @throws Throwable
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

    public function scanEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|exists:employees,key',
        ]);


        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }

        try {
            $employee = Employee::where('key', $request->input('key'))->first();
        } catch (Exception $e) {
            return $this->respondError($e->getMessage(), 'Error');
        }

        return (new EmployeeResource($employee))
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => "employee loaded"
                ]
            ]);
    }
}
