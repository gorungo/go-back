<?php

namespace App\Classes;

class FrontConfig
{
    private array $config = [];

    public function __construct()
    {
        $this->config = [
            'service_mode' => false,
            'auth' => [
                'phone_auth' => config('services.smsru.active'),
                'email_auth' => config('auth.email_auth.active')
            ]
        ];
    }

    public function getConfig(string $section = null): array
    {
        return $section ? $this->config[$section] : $this->config;
    }

}
