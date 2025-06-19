<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Bu yo‘llar CSRF tekshiruvidan chiqariladi.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/webhook', // ← Telegramdan kelayotgan POST so‘rov uchun
    ];
}
