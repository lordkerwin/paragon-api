<?php

namespace App\Http\Transformers;

use App\Models\Event;

class EventTransformer
{
    /**
     * Create or update a pay_rate instance
     *
     * @param  array  $input  Array of key value pairs
     * @param  Event|null  $event  instance (can be null)
     * @return Event
     */
    public static function toInstance(array $input, Event $event = null)
    {
        if (empty($event)) {
            $event = new Event();
        }

        foreach ($input as $key => $value) {
            switch ($key) {
                case 'event_type_id':
                    $event->event_type_id = $value;
                    break;
                case 'employee_id':
                    $event->employee_id = $value;
                    break;
                case 'processed':
                    $event->processed = $value;
                    break;
            }
        }

        return $event;
    }
}
