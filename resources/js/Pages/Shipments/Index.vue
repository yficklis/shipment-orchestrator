<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ShipmentCard from '@/Components/Shipments/ShipmentCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    shipments: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="My Shipments" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    My Shipments
                </h2>
                <Link :href="route('shipments.create')">
                    <PrimaryButton>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Shipment
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Empty State -->
                <div v-if="!shipments.data || shipments.data.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        No shipments yet
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by creating your first shipping label.
                    </p>
                    <div class="mt-6">
                        <Link :href="route('shipments.create')">
                            <PrimaryButton>
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Your First Shipment
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <!-- Shipments Grid -->
                <div v-else class="space-y-4">
                    <ShipmentCard
                        v-for="shipment in shipments.data"
                        :key="shipment.id"
                        :shipment="shipment"
                    />
                </div>

                <!-- Pagination -->
                <div v-if="shipments.data && shipments.data.length > 0 && (shipments.links || shipments.meta)" class="mt-6">
                    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 sm:px-0">
                        <div class="flex w-0 flex-1">
                            <Link
                                v-if="shipments.prev_page_url"
                                :href="shipments.prev_page_url"
                                class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <svg class="mr-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Previous
                            </Link>
                        </div>
                        
                        <div class="hidden md:flex">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing
                                <span class="font-medium">{{ shipments.from || 1 }}</span>
                                to
                                <span class="font-medium">{{ shipments.to || 0 }}</span>
                                of
                                <span class="font-medium">{{ shipments.total || 0 }}</span>
                                results
                            </p>
                        </div>
                        
                        <div class="flex w-0 flex-1 justify-end">
                            <Link
                                v-if="shipments.next_page_url"
                                :href="shipments.next_page_url"
                                class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                Next
                                <svg class="ml-3 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

