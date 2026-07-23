<?php

namespace App\Policies;

use App\Enums\VoucherStatus;
use App\Models\JournalVoucher;
use App\Models\User;

class JournalVoucherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('voucher.view');
    }

    public function view(User $user, JournalVoucher $voucher): bool
    {
        return $user->can('voucher.view');
    }

    public function create(User $user): bool
    {
        return $user->can('voucher.create');
    }

    public function update(User $user, JournalVoucher $voucher): bool
    {
        return $voucher->status === VoucherStatus::Draft && $user->id === $voucher->created_by;
    }

    public function post(User $user, JournalVoucher $voucher): bool
    {
        return $voucher->status === VoucherStatus::Draft && $user->can('voucher.post');
    }

    public function reverse(User $user, JournalVoucher $voucher): bool
    {
        return $voucher->status === VoucherStatus::Posted && $user->can('voucher.reverse');
    }
}
