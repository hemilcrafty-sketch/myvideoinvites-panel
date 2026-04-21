<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * HS256 JWT for app user sessions (AuthController) and compatible admin tokens.
 * Set JWT_SECRET in .env; if unset, derives from APP_KEY (base64 decoded when prefixed).
 */
class JwtHelper
{
    public static function secret(): string
    {
        $s = env('JWT_SECRET');
        if (is_string($s) && $s !== '') {
            return $s;
        }

        $key = config('app.key', '');
        if (! is_string($key) || $key === '') {
            throw new \RuntimeException('Set JWT_SECRET in .env or ensure APP_KEY is set for JwtHelper.');
        }

        if (strpos($key, 'base64:') === 0) {
            $decoded = base64_decode(substr($key, 7), true);

            return $decoded !== false ? $decoded : $key;
        }

        return $key;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function generate(array $payload, int $expiryDays = 30): string
    {
        $now = time();
        $claims = array_merge($payload, [
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + ($expiryDays * 86400),
        ]);

        return JWT::encode($claims, self::secret(), 'HS256');
    }

    public static function decode(string $token): object
    {
        return JWT::decode($token, new Key(self::secret(), 'HS256'));
    }
}
