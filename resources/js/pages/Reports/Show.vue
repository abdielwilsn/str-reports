<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, FileImage, ImageOff } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import type { BadgeVariants } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { dashboard } from '@/routes';
import { index } from '@/routes/reports';

type Deposit = {
    id: number;
    amount: number;
    status: string;
    cryptocurrency: string | null;
    transaction_hash: string | null;
    proof_of_payment_url: string | null;
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

const previewUrl = ref<string | null>(null);
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
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Card v-for="deposit in deposits.data" :key="deposit.id" class="gap-0 overflow-hidden p-0">
                    <button
                        type="button"
                        class="block aspect-video w-full cursor-pointer bg-muted"
                        :disabled="!deposit.proof_of_payment_url"
                        @click="previewUrl = deposit.proof_of_payment_url"
                    >
                        <img
                            v-if="deposit.proof_of_payment_url"
                            :src="deposit.proof_of_payment_url"
                            :alt="`Proof of payment for deposit #${deposit.id}`"
                            class="size-full object-cover"
                        />
                        <div
                            v-else
                            class="flex size-full flex-col items-center justify-center gap-1 text-muted-foreground"
                        >
                            <ImageOff class="size-6" />
                            <span class="text-xs">No proof uploaded</span>
                        </div>
                    </button>

                    <div class="flex flex-col gap-2 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-semibold">
                                {{ currency.format(deposit.amount) }}
                            </p>
                            <Badge :variant="statusVariant[deposit.status] ?? 'outline'">
                                {{ deposit.status }}
                            </Badge>
                        </div>
                        <p v-if="deposit.cryptocurrency" class="text-sm text-muted-foreground">
                            {{ deposit.cryptocurrency }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{
                                deposit.created_at
                                    ? dateFormat.format(new Date(deposit.created_at))
                                    : '—'
                            }}
                        </p>
                    </div>
                </Card>
            </div>
            <Card v-else class="items-center gap-2 px-6 py-10 text-center">
                <FileImage class="size-6 text-muted-foreground" />
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

    <Dialog :open="!!previewUrl" @update:open="(open) => !open && (previewUrl = null)">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>Proof of payment</DialogTitle>
            </DialogHeader>
            <img
                v-if="previewUrl"
                :src="previewUrl"
                alt="Proof of payment"
                class="w-full rounded-lg border border-border"
            />
        </DialogContent>
    </Dialog>
</template>
