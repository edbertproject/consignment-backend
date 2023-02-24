<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CodeRule implements Rule
{
    protected string $message = ':attribute must uppercase';

    public function __construct(protected $symbol=true,
                                protected $whitespace=false,
                                protected $uppercase=true)
    {
        //
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
        if ($this->uppercase && preg_match('/[a-z]/', $value)) {
            return false;
        }

        if (!$this->whitespace && preg_match('/\s+/', $value)) {
            $this->message = ':attribute must not contain whitespace';
            return false;
        }

        if (!$this->symbol && preg_match('/[^a-zA-Z0-9]+/', $value)) {
            $this->message = ':attribute must not contain any symbol';
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
