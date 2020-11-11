<?php

namespace App\Http\Controllers;

use App\Http\Transformers\EmployeeTransformer;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Shift;
use App\Models\ShiftStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftController extends BaseController
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


    public function createShift(Event $clock_in_event, Event $clock_out_event)
    {
        DB::beginTransaction();
        try {

            $employee = Employee::find($clock_in_event->employee_id);

            $shift = new Shift();
            $shift->employee_id = $clock_in_event->employee_id;
            $shift->start = $clock_in_event->created_at;
            $shift->end = $clock_out_event->created_at;
            $shift->pay_rate = $employee->getCurrentPayRate($clock_in_event->created_at)->rate;
            $shift->value = 0;
            $shift->shift_status_id = ShiftStatus::retrieve('new', 'id');
            $shift->processed = false;
            $shift->payroll_locked = false;
            $shift->save();
            DB::commit();
            return $shift;
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $ex->getMessage();
        }
    }


    public function update(Request $request, Shift $shift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {
        //
    }
}
