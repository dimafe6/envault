<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CheckTokenTrait;
use App\Models\App;

class SetupAppController extends Controller
{
    use CheckTokenTrait;

    /**
     * @param App $app
     * @param string $token
     * @return array
     */
    public function __invoke(App $app, $token)
    {
        $setupToken = $this->checkToken($app, $token);

        return [
            'authToken' => $setupToken->user->createToken(uniqid())->plainTextToken,
            'app'       => $setupToken->app->load(['variables', 'files']),
        ];
    }
}
