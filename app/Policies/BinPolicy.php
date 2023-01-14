<?php

namespace App\Policies;

use App\Models\Bin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BinPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function manage(?User $user, Bin $bin)
    {
        if (!$user && session()->has('edit_hash')) {
            return optional($bin->editHash)->hash === session()->get('edit_hash');
        }

        return optional($user)->id === $bin->user_id;
    }
}
