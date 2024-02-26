<?php

namespace App\Registry;

class DTORegistry
{
    private array $store = [];

    public function set(string $key, $value): void
    {
        $this->store[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->store[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->store[$key]);
    }
}