<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class ValidAssignee implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);

        // The 'exists:users,id' rule should ideally catch if $user is null,
        // but a defensive check here is fine.
        if (!$user) {
            // This case should ideally not be hit if 'exists' rule runs first.
            // If it can be hit, the message might need to be more generic
            // or rely on the 'exists' rule's message.
            // For now, let's assume 'exists' handles non-existence.
            return;
        }

        if (!in_array($user->user_type, ['it', 'vendor'])) {
            $fail('The selected :attribute is not eligible to be an assignee (must be IT or Vendor).');
        }
    }
}
