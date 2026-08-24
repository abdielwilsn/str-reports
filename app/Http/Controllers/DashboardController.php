<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalDeposited = (float) Deposit::query()->where('status', Deposit::STATUS_CONFIRMED)->sum('amount');
        $totalWithdrawn = (float) Withdrawal::query()->where('status', Withdrawal::STATUS_COMPLETED)->sum('amount');

        $topUsers = User::query()
            ->withSum(['deposits as total_deposited' => function ($query) {
                $query->where('status', Deposit::STATUS_CONFIRMED);
            }], 'amount')
            ->withSum(['withdrawals as total_withdrawn' => function ($query) {
                $query->where('status', Withdrawal::STATUS_COMPLETED);
            }], 'amount')
            ->orderByDesc('total_deposited')
            ->limit(5)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_deposited' => (float) ($user->total_deposited ?? 0),
                'total_withdrawn' => (float) ($user->total_withdrawn ?? 0),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'deposited' => $totalDeposited,
                'withdrawn' => $totalWithdrawn,
                'net' => $totalDeposited - $totalWithdrawn,
            ],
            'topUsers' => $topUsers,
        ]);
    }
}
