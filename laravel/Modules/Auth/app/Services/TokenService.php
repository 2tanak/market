<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Redis;
use Modules\Auth\Model\Users\Model as User;

class TokenService
{
    public function verify_token(User $authUser, string $tokenName = 'web-access'): string
    {
        $redisKey = "user:{$authUser->id}:token";
        $tokenInstance = $authUser->tokens()->where('name', $tokenName)->first();

        if ($tokenInstance) {
            if ($tokenInstance->created_at->addHours(24)->isPast()) {
                $tokenInstance->delete();
                Redis::del($redisKey);

                return $this->issueToken($authUser, $tokenName, $redisKey);
            }

            $token = Redis::get($redisKey);
            if ($token) {
                return $token;
            }

            $tokenInstance->delete();
        }

        return $this->issueToken($authUser, $tokenName, $redisKey);
    }

    public function revoke_token(User $authUser, string $tokenName = 'web-access'): void
    {
        $authUser->tokens()->where('name', $tokenName)->delete();
        Redis::del("user:{$authUser->id}:token");
    }

    protected function issueToken(User $authUser, string $tokenName, string $redisKey): string
    {
        $token = $authUser->createToken($tokenName)->plainTextToken;
        Redis::setex($redisKey, 86400, $token);

        return $token;
    }
}
