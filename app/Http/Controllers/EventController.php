<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Transformers\EventTransformer;
use App\Models\Employee;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventController extends BaseController
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
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'event_type_id' => 'required|exists:event_types,id'
        ]);

        if ($validator->fails()) {
            return $this->respondError($validator->errors()->toArray(), "Error validating input", 422);
        }

        // before we store the event, lets check to see what the available events are
        try {
            $employee = Employee::find($request->input('employee_id'));
        } catch (Exception $ex) {
            return $this->respondError($ex->getMessage(), "Error finding employee", 400);
        }

        $available_events = $employee->getAvailableEvents();
        if (!$available_events->contains('id', $request->input('event_type_id'))) {
            // if we're here, then the $available_events collection doesn't contain
            // an event_type_id that matches what has been requested. Return an error that the event is not permitted
            return $this->respondError('', 'Sorry, you cannot create an event of that type', 400);
        }


        DB::beginTransaction();
        try {
            $event = EventTransformer::toInstance($request->all());
            $event->save();
            DB::commit();
        } catch (Exception $ex) {
            Log::info($ex->getMessage());
            DB::rollBack();
            return $this->respondError(null, $ex->getMessage(), 409);
        }

        return (new EventResource($event))
            ->additional([
                'meta' => [
                    'success' => true,
                    'message' => "event created"
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
