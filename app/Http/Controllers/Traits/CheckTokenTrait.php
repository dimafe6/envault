<?php

namespace App\Http\Controllers\Traits;

use App\Models\App;
use App\Models\AppSetupToken;

trait CheckTokenTrait
{
    /**
     * @param App $app
     * @param string $token
     * @return AppSetupToken
     */
    public function checkToken(App $app, string $token)
    {
        /** @var ?AppSetupToken $setupToken */
        $setupToken = $app->setup_tokens()->where([
            ['created_at', '>=', now()->subMinutes($app->token_lifetime)],
            ['token', $token],
        ])->firstOrFail();

        if ($setupToken->user->cannot('view', $app)) {
            abort(403);
        }

        return $setupToken;
    }
}
