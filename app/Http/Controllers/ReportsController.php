<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportsController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->withSum(['deposits as total_deposited' => function ($query) {
                $query->where('status', Deposit::STATUS_CONFIRMED);
            }], 'amount')
            ->withSum(['withdrawals as total_withdrawn' => function ($query) {
                $query->where('status', Withdrawal::STATUS_COMPLETED);
            }], 'amount')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('total_deposited')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'total_deposited' => (float) ($user->total_deposited ?? 0),
                'total_withdrawn' => (float) ($user->total_withdrawn ?? 0),
            ]);

        return Inertia::render('Reports/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
            'totals' => [
                'deposited' => (float) Deposit::query()->where('status', Deposit::STATUS_CONFIRMED)->sum('amount'),
                'withdrawn' => (float) Withdrawal::query()->where('status', Withdrawal::STATUS_COMPLETED)->sum('amount'),
            ],
        ]);
    }

    public function show(User $user): Response
    {
        $deposits = $user->deposits()
            ->with('cryptocurrency')
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Deposit $deposit) => [
                'id' => $deposit->id,
                'amount' => (float) $deposit->amount,
                'status' => $deposit->status,
                'cryptocurrency' => $deposit->cryptocurrency?->symbol,
                'transaction_hash' => $deposit->transaction_hash,
                'created_at' => $deposit->created_at?->toIso8601String(),
                'verified_at' => $deposit->verified_at?->toIso8601String(),
            ]);

        return Inertia::render('Reports/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'totals' => [
                'deposited' => (float) $user->deposits()->where('status', Deposit::STATUS_CONFIRMED)->sum('amount'),
                'withdrawn' => (float) $user->withdrawals()->where('status', Withdrawal::STATUS_COMPLETED)->sum('amount'),
            ],
            'deposits' => $deposits,
        ]);
    }
}
