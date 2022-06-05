<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CountryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    /**
     * Determine whether the user can create countries.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('countries.create')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any country.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create countries.
     *
     * @return mixed
     */
    public function update(User $user)
    {
        if ($user->can('countries.edit')) {
            return true;
        }
    }
}
