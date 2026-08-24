<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes';
import { index, show } from '@/routes/reports';

type UserTotals = {
    id: number;
    name: string;
    email: string;
    total_deposited: number;
    total_withdrawn: number;
};

type PaginatedUsers = {
    data: UserTotals[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
};

const props = defineProps<{
    users: PaginatedUsers;
    filters: { search: string };
    totals: { deposited: number; withdrawn: number };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Reports', href: index() },
        ],
    },
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(
        index().url,
        { search: value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});
</script>

<template>
    <Head title="Deposit &amp; withdrawal totals" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Deposit &amp; withdrawal totals"
            description="Confirmed deposits and completed withdrawals, per user."
        />

        <div class="grid gap-4 sm:grid-cols-2">
            <Card class="gap-2 px-6">
                <p class="text-sm text-muted-foreground">
                    Total confirmed deposits
                </p>
                <p class="text-2xl font-semibold">
                    {{ currency.format(props.totals.deposited) }}
                </p>
            </Card>
            <Card class="gap-2 px-6">
                <p class="text-sm text-muted-foreground">
                    Total completed withdrawals
                </p>
                <p class="text-2xl font-semibold">
                    {{ currency.format(props.totals.withdrawn) }}
                </p>
            </Card>
        </div>

        <Input
            v-model="search"
            type="search"
            placeholder="Search by name or email…"
            class="max-w-sm"
        />

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Total deposited</th>
                        <th class="px-4 py-3 font-medium">Total withdrawn</th>
                        <th class="px-4 py-3 font-medium">Net</th>
                    </tr>
                </thead>
                <tbody>
                    <Link
                        v-for="user in props.users.data"
                        :key="user.id"
                        :href="show(user.id)"
                        as="tr"
                        class="cursor-pointer border-b border-sidebar-border/70 last:border-0 hover:bg-accent/50 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ user.name }}</div>
                            <div class="text-muted-foreground">{{ user.email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            {{ currency.format(user.total_deposited) }}
                        </td>
                        <td class="px-4 py-3">
                            {{ currency.format(user.total_withdrawn) }}
                        </td>
                        <td class="px-4 py-3">
                            {{
                                currency.format(
                                    user.total_deposited - user.total_withdrawn,
                                )
                            }}
                        </td>
                    </Link>
                    <tr v-if="props.users.data.length === 0">
                        <td
                            class="px-4 py-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="props.users.links.length > 3"
            class="flex flex-wrap gap-1"
        >
            <template v-for="link in props.users.links" :key="link.label">
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
