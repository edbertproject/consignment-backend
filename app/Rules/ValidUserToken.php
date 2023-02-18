<?php

namespace App\Rules;

use App\Entities\User;
use App\Entities\UserToken;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ValidUserToken implements Rule
{
    protected $email;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email, $message = null)
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::where('email', $this->email)->first();
        $token = UserToken::where('user_id', $user->id)->first();

        if(empty($token)) {
            $this->message = 'You already have created password';
            return false;
        }

        if(!Hash::check($value, $token->token)) {
            $this->message = 'User token is invalid';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
