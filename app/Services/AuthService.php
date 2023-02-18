<?php

namespace App\Services;

use App\Entities\User;
use App\Utils\Constants;
use Closure;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use UnexpectedValueException;

class AuthService
{
    public static function verifyUser($email, $password)
    {
        $user = User::query()
            ->where('email', $email)
            ->whereHas('roleUser', function ($query) {
                $query->where('role_id', '!=', Constants::ROLE_PUBLIC);
            })
            ->first();

        if (! $user) {
            return false;
        }

        $verifyPassword = Hash::check($password, $user->password);
        if (! $verifyPassword) {
            return false;
        }

        return true;
    }

    public static function reset(array $credentials, Closure $callback)
    {
        $user = static::validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $password = $credentials['password'];
        $callback($user, $password);

        static::delete($user);

        return PasswordBroker::PASSWORD_RESET;
    }

    protected static function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token', 'password']);

        $user = static::retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    protected static function deleteExisting(User $user)
    {
        $table = config('auth.passwords.users.table');

        return DB::table($table)->where('email', $user->email)->delete();
    }

    protected static function validateReset(array $credentials)
    {
        if (is_null($user = static::getUser($credentials))) {
            return PasswordBroker::INVALID_USER;
        }

        if (! static::exists($user, $credentials['token'])) {
            return PasswordBroker::INVALID_TOKEN;
        }

        return $user;
    }

    protected static function exists(CanResetPasswordContract $user, $token)
    {
        $table = config('auth.passwords.users.table');
        $hasher = app('hash');

        $record = DB::table($table)->where('email', $user->getEmailForPasswordReset())->first();

        return $record && ! static::tokenExpired($record->created_at) && $hasher->check($token, $record->token);
    }

    protected static function tokenExpired($createdAt)
    {
        $expires = config('auth.passwords.users.expire');

        return Carbon::parse($createdAt)->addSeconds($expires * 60)->isPast();
    }

    protected static function delete(User $user)
    {
        static::deleteExisting($user);
    }

    protected static function retrieveByCredentials(array $credentials)
    {
        $isAdmin = ! empty($credentials['is_admin']) ? $credentials['is_admin'] : 0;

        $query = User::query()
            ->where('email', $credentials['email'])
            ->whereHas('roleUser', function ($query) use ($isAdmin) {
                $query->when(! $isAdmin, function ($queryWhen) {
                    $queryWhen->where('role_id', Constants::ROLE_PUBLIC);
                }, function ($queryWhen) {
                    $queryWhen->where('role_id', '!=', Constants::ROLE_PUBLIC);
                });
            });

        return $query->first();
    }
}
