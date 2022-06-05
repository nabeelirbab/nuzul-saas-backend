<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    /**
     * Determine whether the user can create cities.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('cities.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any cities.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create cities.
     *
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->can('cities.edit')) {
            return true;
        }
    }
}
