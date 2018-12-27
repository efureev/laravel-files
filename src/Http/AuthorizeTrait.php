<?php

namespace Feugene\Files\Http;

use Feugene\Files\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait AuthorizeTrait
{
    use AuthorizesRequests;

    /**
     * @param string $action
     * @param array  $params
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @codeCoverageIgnore
     */
    protected function authorizeAction(string $action, array $params = [])
    {
        $this->authorize($action, [File::class, $params]);
    }

}
