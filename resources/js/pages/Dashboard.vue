<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Scale, TrendingDown, TrendingUp, Users } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as reportsIndex, show as reportsShow } from '@/routes/reports';

type UserTotals = {
    id: number;
    name: string;
    email: string;
    total_deposited: number;
    total_withdrawn: number;
};

defineProps<{
    stats: {
        users: number;
        deposited: number;
        withdrawn: number;
        net: number;
    };
    topUsers: UserTotals[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card class="gap-2 px-6">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Users class="size-4" />
                    Users
                </div>
                <p class="text-2xl font-semibold">{{ stats.users }}</p>
            </Card>
            <Card class="gap-2 px-6">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <TrendingUp class="size-4 text-primary" />
                    Confirmed deposits
                </div>
                <p class="text-2xl font-semibold">
                    {{ currency.format(stats.deposited) }}
                </p>
            </Card>
            <Card class="gap-2 px-6">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <TrendingDown class="size-4 text-destructive" />
                    Completed withdrawals
                </div>
                <p class="text-2xl font-semibold">
                    {{ currency.format(stats.withdrawn) }}
                </p>
            </Card>
            <Card class="gap-2 px-6">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Scale class="size-4" />
                    Net
                </div>
                <p class="text-2xl font-semibold">
                    {{ currency.format(stats.net) }}
                </p>
            </Card>
        </div>

        <Card class="gap-4 px-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium">Top depositors</p>
                    <p class="text-sm text-muted-foreground">
                        Users with the highest confirmed deposit totals.
                    </p>
                </div>
                <Link :href="reportsIndex()">
                    <Button variant="ghost" size="sm" class="gap-1">
                        View all
                        <ArrowRight class="size-4" />
                    </Button>
                </Link>
            </div>

            <div v-if="topUsers.length" class="flex flex-col divide-y divide-border">
                <Link
                    v-for="user in topUsers"
                    :key="user.id"
                    :href="reportsShow(user.id)"
                    class="flex items-center justify-between rounded-lg py-3 text-sm hover:bg-accent/50"
                >
                    <div>
                        <p class="font-medium">{{ user.name }}</p>
                        <p class="text-muted-foreground">{{ user.email }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-primary">
                            {{ currency.format(user.total_deposited) }}
                        </p>
                        <p class="text-muted-foreground">
                            {{ currency.format(user.total_withdrawn) }} withdrawn
                        </p>
                    </div>
                </Link>
            </div>
            <p v-else class="py-6 text-center text-sm text-muted-foreground">
                No deposits yet.
            </p>
        </Card>
    </div>
</template>
