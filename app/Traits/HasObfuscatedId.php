<?php

namespace App\Traits;

use App\Services\Security\SecureId;

trait HasObfuscatedId
{
    public function getRouteKey(): string
    {
        return SecureId::encode((int) $this->getKey(), static::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field && $field !== $this->getRouteKeyName()) {
            return parent::resolveRouteBinding($value, $field);
        }

        $id = SecureId::decode((string) $value, static::class);

        if (!$id) {
            if (is_numeric($value)) {
                $id = (int) $value;
            } else {
                abort(404);
            }
        }

        return $this->where($this->getRouteKeyName(), $id)->firstOrFail();
    }

    public function getObfuscatedIdAttribute(): string
    {
        return SecureId::encode((int) $this->getKey(), static::class);
    }

    public static function findByObfuscatedId(string $token)
    {
        $id = SecureId::decode($token, static::class);
        return $id ? static::find($id) : null;
    }

    public static function findByObfuscatedIdOrFail(string $token)
    {
        $id = SecureId::decode($token, static::class);
        if (!$id) {
            abort(404);
        }
        return static::findOrFail($id);
    }
}
