<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LabelPreview from '@/Components/Shipments/LabelPreview.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    shipment: {
        type: Object,
        required: true,
    },
});

// Extract shipment data from resource wrapper
const shipmentData = props.shipment.data || props.shipment;

const showDeleteConfirm = ref(false);
const deleting = ref(false);

const deleteShipment = () => {
    deleting.value = true;
    router.delete(route('shipments.destroy', shipmentData.id), {
        onFinish: () => {
            deleting.value = false;
            showDeleteConfirm.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Shipment ${shipmentData.tracking_code || shipmentData.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        :href="route('shipments.index')"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center gap-1 mb-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Shipments
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Shipment Details
                    </h2>
                </div>
                
                <div class="flex gap-3">
                    <Link :href="route('shipments.create')">
                        <SecondaryButton>
                            Create Another
                        </SecondaryButton>
                    </Link>
                    
                    <DangerButton
                        v-if="!showDeleteConfirm"
                        @click="showDeleteConfirm = true"
                        type="button"
                    >
                        Delete
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Delete Confirmation -->
                <div v-if="showDeleteConfirm" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                Delete this shipment?
                            </h3>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                This action cannot be undone. The shipment record will be permanently removed from your history.
                            </p>
                            <div class="mt-4 flex gap-3">
                                <DangerButton
                                    @click="deleteShipment"
                                    :disabled="deleting"
                                >
                                    <span v-if="deleting">Deleting...</span>
                                    <span v-else>Yes, Delete</span>
                                </DangerButton>
                                <SecondaryButton
                                    @click="showDeleteConfirm = false"
                                    :disabled="deleting"
                                >
                                    Cancel
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Label Preview -->
                <LabelPreview :shipment="shipmentData" />

                <!-- Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Timeline
                    </h3>
                    
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                                    Shipment created and label purchased
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                {{ new Date(shipmentData.created_at).toLocaleString() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Shipment Information
                    </h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Shipment ID
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                                {{ shipmentData.id }}
                            </dd>
                        </div>
                        
                        <div v-if="shipmentData.easypost_shipment_id">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                EasyPost ID
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                                {{ shipmentData.easypost_shipment_id }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Created
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ new Date(shipmentData.created_at).toLocaleString() }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Last Updated
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ new Date(shipmentData.updated_at).toLocaleString() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

