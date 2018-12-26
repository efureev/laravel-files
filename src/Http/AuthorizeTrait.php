<?php

namespace Feugene\Files\Http;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait AuthorizeTrait
{
    use AuthorizesRequests;

    /**
     * @param string $action
     * @param array  $params
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeAction(string $action, array $params = [])
    {
        $this->authorize($action, $params);
    }

}