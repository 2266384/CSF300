<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class TokenService
{
    /**
     * Generate API token for a given model instance.
     *
     * @param  Model  $model
     * @param  string  $tokenName
     * @return string
     */
    public function generateToken(Model $model, string $tokenName = 'DefaultApp')
    {
        // Generate a token for the given model instance
        return $model->createToken($tokenName)->plainTextToken;
    }
}
