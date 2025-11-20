<script setup>
import { computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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

const openLabel = () => {
    if (props.shipment.label?.postage_label_url) {
        window.open(props.shipment.label.postage_label_url, '_blank');
    }
};

const openTracking = () => {
    if (props.shipment.label?.tracking_url) {
        window.open(props.shipment.label.tracking_url, '_blank');
    }
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-blue to-blue-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Shipping Label</h2>
                    <p class="text-blue-100 mt-1">{{ shipment.carrier }} - {{ shipment.tracking_code || 'N/A' }}</p>
                </div>
                <div>
                    <span :class="[statusColor, 'px-3 py-1 rounded-full text-sm font-semibold uppercase']">
                        {{ shipment.status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Addresses -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- From Address -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">
                        From
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ shipment.from_address.name }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">
                            {{ shipment.from_address.street1 }}
                        </p>
                        <p v-if="shipment.from_address.street2" class="text-gray-700 dark:text-gray-300">
                            {{ shipment.from_address.street2 }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ shipment.from_address.city }}, {{ shipment.from_address.state }} {{ shipment.from_address.zip }}
                        </p>
                    </div>
                </div>

                <!-- To Address -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">
                        To
                    </h3>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ shipment.to_address.name }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300 mt-1">
                            {{ shipment.to_address.street1 }}
                        </p>
                        <p v-if="shipment.to_address.street2" class="text-gray-700 dark:text-gray-300">
                            {{ shipment.to_address.street2 }}
                        </p>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ shipment.to_address.city }}, {{ shipment.to_address.state }} {{ shipment.to_address.zip }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Package Details -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">
                    Package Details
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Weight</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ shipment.package.weight }} oz
                            </p>
                        </div>
                        <div v-if="shipment.package.length">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Length</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ shipment.package.length }}"
                            </p>
                        </div>
                        <div v-if="shipment.package.width">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Width</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ shipment.package.width }}"
                            </p>
                        </div>
                        <div v-if="shipment.package.height">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Height</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ shipment.package.height }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost -->
            <div v-if="shipment.rate_amount">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">
                    Shipping Cost
                </h3>
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                    <p class="text-2xl font-bold text-brand-blue">
                        ${{ Number(shipment.rate_amount).toFixed(2) }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <PrimaryButton
                    v-if="shipment.label?.postage_label_url"
                    @click="openLabel"
                    class="flex-1"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Label
                </PrimaryButton>
                
                <PrimaryButton
                    v-if="shipment.label?.tracking_url"
                    @click="openTracking"
                    class="flex-1 bg-gray-600 hover:bg-gray-700"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Track Package
                </PrimaryButton>
            </div>
        </div>
    </div>
</template>

