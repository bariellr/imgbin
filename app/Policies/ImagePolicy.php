<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
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

    public function manage(?User $user, Image $image)
    {
        if (!$user && session()->has('edit_hash')) {
            return optional($image->bin->editHash)->hash === session()->get('edit_hash');
        }

        return optional($user)->id === $image->bin->user_id;
    }
}
