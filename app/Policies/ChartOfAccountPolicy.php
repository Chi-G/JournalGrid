<?php

namespace App\Policies;

use App\Models\User;

class ChartOfAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->can('chart-of-accounts.manage');
    }
}
