<?php

namespace App\Http\Transformers;

use App\Models\Employee;

class EmployeeTransformer
{
    /**
     * Create or update a employee instance
     *
     * @param Array $input Array of key value pairs
     * @param Employee $employee employee instance (can be null)
     * @return Employee
     */
    public static function toInstance(array $input, $employee = null)
    {
        if (empty($employee)) {
            $employee = new Employee();
        }

        foreach ($input as $key => $value) {
            switch ($key) {
                case 'name':
                    $employee->name = $value;
                    break;
                case 'profile_image_url':
                    $employee->profile_image_url = $value;
                    break;
                case 'role_id':
                    $employee->role_id = $value;
                    break;
                case 'department_id':
                    $employee->department_id = $value;
                    break;
                case 'employee_type_id':
                    $employee->employee_type_id = $value;
                    break;
                case 'active':
                    $employee->active = $value;
                    break;
            }
        }

        return $employee;
    }
}
