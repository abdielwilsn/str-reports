<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import type { BadgeVariants } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index } from '@/routes/reports';

type Deposit = {
    id: number;
    amount: number;
    status: string;
    cryptocurrency: string | null;
    transaction_hash: string | null;
    created_at: string | null;
    verified_at: string | null;
};

type PaginatedDeposits = {
    data: Deposit[];
    links: { url: string | null; label: string; active: boolean }[];
};

defineProps<{
    user: { id: number; name: string; email: string };
    totals: { deposited: number; withdrawn: number };
    deposits: PaginatedDeposits;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Reports', href: index() },
        ],
    },
});

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

const dateFormat = new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

const statusVariant: Record<string, BadgeVariants['variant']> = {
    confirmed: 'default',
    pending: 'secondary',
    verifying: 'secondary',
    failed: 'destructive',
    mismatch: 'destructive',
};
</script>

<template>
    <Head :title="`${user.name} — Deposits`" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <Link
                :href="index()"
                class="inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="size-4" />
                Back to reports
            </Link>
        </div>

        <Heading :title="user.name" :description="user.email" />

        <div class="grid gap-4 sm:grid-cols-2">
            <Card class="gap-2 px-6">
                <p class="text-sm text-muted-foreground">Total confirmed deposits</p>
                <p class="text-2xl font-semibold">
                    {{ currency.format(totals.deposited) }}
                </p>
            </Card>
            <Card class="gap-2 px-6">
                <p class="text-sm text-muted-foreground">Total completed withdrawals</p>
                <p class="text-2xl font-semibold">
                    {{ currency.format(totals.withdrawn) }}
                </p>
            </Card>
        </div>

        <div>
            <p class="mb-3 font-medium">Deposits</p>

            <div
                v-if="deposits.data.length"
                class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                        <tr>
                            <th class="px-4 py-3 font-medium">Amount</th>
                            <th class="px-4 py-3 font-medium">Currency</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="deposit in deposits.data"
                            :key="deposit.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ currency.format(deposit.amount) }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ deposit.cryptocurrency ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge :variant="statusVariant[deposit.status] ?? 'outline'">
                                    {{ deposit.status }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{
                                    deposit.created_at
                                        ? dateFormat.format(new Date(deposit.created_at))
                                        : '—'
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Card v-else class="items-center gap-2 px-6 py-10 text-center">
                <p class="text-sm text-muted-foreground">
                    This user has no deposits yet.
                </p>
            </Card>
        </div>

        <div v-if="deposits.links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="link in deposits.links" :key="link.label">
                <button
                    v-if="link.url"
                    v-html="link.label"
                    class="rounded-md border border-sidebar-border/70 px-3 py-1 text-sm dark:border-sidebar-border"
                    :class="{ 'bg-accent': link.active }"
                    @click="router.get(link.url, {}, { preserveState: true, preserveScroll: true })"
                />
                <span
                    v-else
                    v-html="link.label"
                    class="rounded-md px-3 py-1 text-sm text-muted-foreground"
                />
            </template>
        </div>
    </div>
</template>
