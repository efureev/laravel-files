<?php

namespace Feugene\Files\Policies;

use Feugene\Files\Models\File;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class FilePolicy
 *
 * @package Feugene\Files\Policies
 */
class FilePolicy
{
    use HandlesAuthorization;

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param string                                          $policyName
     * @param mixed                                           $data
     */
    public function before(?Authenticatable $user, string $policyName, $data)
    {
    }


    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param \Feugene\Files\Models\File                      $model
     *
     * @return bool
     */
    public function delete(?Authenticatable $user, File $model): bool
    {
        return true;
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param \Feugene\Files\Models\File                      $model
     *
     * @return bool
     */
    public function upload(?Authenticatable $user, File $model): bool
    {
        return true;
    }
}
