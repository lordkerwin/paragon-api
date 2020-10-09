<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;


    public function events()
    {
        return $this->hasMany(Event::class);
    }


    public static function retrieve($name, $field = null)
    {
        $record = EventType::where('name', $name)->firstOrFail();

        if (!is_null($field)) {
            return $record->{$field};
        }

        return $record;
    }

}
