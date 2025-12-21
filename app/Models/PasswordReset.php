<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'password_reset';
    public $timestamps = false;

    protected $fillable = ['email', 'token', 'created_at'];

    public static function createToken(string $email): string
    {
        $token = \Str::random(64);

        // Remove old tokens for this email
        self::where('email', $email)->delete();

        // Create new token
        self::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        return $token;
    }

    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)
            ->where('created_at', '>', now()->subHours(1)) // Valid for 1 hour
            ->first();
    }
}
