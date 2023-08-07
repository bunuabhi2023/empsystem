<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Above18YearsOldRule implements Rule
{
    public function passes($attribute, $value)
    {
        $dateOfBirth = $value;
        $currentDate = now();

        $age = $currentDate->diff(date_create($dateOfBirth));
        $yearsDiff = $age->y;

        if ($yearsDiff >= 18) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return 'The :attribute must be above 18 years old.';
    }
}
