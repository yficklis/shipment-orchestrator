<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    shipment: {
        type: Object,
        required: true,
    },
});

const statusColor = computed(() => {
    switch (props.shipment.status) {
        case 'purchased':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'voided':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
    }
});

const formattedDate = computed(() => {
    return new Date(props.shipment.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
});
</script>

<template>
    <Link
        :href="route('shipments.show', shipment.id)"
        class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200"
    >
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ shipment.tracking_code || 'No Tracking' }}
                        </h3>
                        <span :class="[statusColor, 'px-2 py-1 rounded text-xs font-semibold uppercase']">
                            {{ shipment.status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ shipment.carrier }} ? {{ formattedDate }}
                    </p>
                </div>
                
                <div v-if="shipment.rate_amount" class="text-right">
                    <p class="text-lg font-bold text-brand-blue">
                        ${{ Number(shipment.rate_amount).toFixed(2) }}
                    </p>
                </div>
            </div>

            <!-- Addresses -->
            <div class="grid md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <!-- From -->
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">
                        From
                    </p>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                        {{ shipment.from_address.name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ shipment.from_address.city }}, {{ shipment.from_address.state }}
                    </p>
                </div>

                <!-- To -->
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-1">
                        To
                    </p>
                    <p class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                        {{ shipment.to_address.name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ shipment.to_address.city }}, {{ shipment.to_address.state }}
                    </p>
                </div>
            </div>

            <!-- Package Info -->
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>{{ shipment.package.weight }} oz</span>
                    </div>
                    
                    <div v-if="shipment.package.length && shipment.package.width && shipment.package.height" class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        <span>{{ shipment.package.length }}" × {{ shipment.package.width }}" × {{ shipment.package.height }}"</span>
                    </div>
                </div>
            </div>
        </div>
    </Link>
</template>

