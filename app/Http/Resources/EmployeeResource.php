<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'key' => $this->key,
            'active' => $this->active,
            'department' => new DepartmentResource($this->department) ?? null,
            'role' => new RoleResource($this->role) ?? null,
            'employee_type' => new EmployeeTypeResource($this->employeeType) ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at ?? null
        ];
    }
}
