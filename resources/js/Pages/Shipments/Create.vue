<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AddressForm from '@/Components/Shipments/AddressForm.vue';
import PackageForm from '@/Components/Shipments/PackageForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    // From address
    from_name: '',
    from_street1: '',
    from_street2: '',
    from_city: '',
    from_state: '',
    from_zip: '',
    from_phone: '',
    from_email: '',
    
    // To address
    to_name: '',
    to_street1: '',
    to_street2: '',
    to_city: '',
    to_state: '',
    to_zip: '',
    to_phone: '',
    to_email: '',
    
    // Package
    weight: '',
    length: '',
    width: '',
    height: '',
});

const fromAddress = {
    get name() { return form.from_name; },
    get street1() { return form.from_street1; },
    get street2() { return form.from_street2; },
    get city() { return form.from_city; },
    get state() { return form.from_state; },
    get zip() { return form.from_zip; },
    get phone() { return form.from_phone; },
    get email() { return form.from_email; },
};

const toAddress = {
    get name() { return form.to_name; },
    get street1() { return form.to_street1; },
    get street2() { return form.to_street2; },
    get city() { return form.to_city; },
    get state() { return form.to_state; },
    get zip() { return form.to_zip; },
    get phone() { return form.to_phone; },
    get email() { return form.to_email; },
};

const packageDetails = {
    get weight() { return form.weight; },
    get length() { return form.length; },
    get width() { return form.width; },
    get height() { return form.height; },
};

const updateFromAddress = (value) => {
    Object.keys(value).forEach(key => {
        form[`from_${key}`] = value[key];
    });
};

const updateToAddress = (value) => {
    Object.keys(value).forEach(key => {
        form[`to_${key}`] = value[key];
    });
};

const updatePackage = (value) => {
    Object.keys(value).forEach(key => {
        form[key] = value[key];
    });
};

const submit = () => {
    form.post(route('shipments.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create Shipment" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Create New Shipment
                </h2>
                <Link :href="route('shipments.index')">
                    <SecondaryButton>
                        Cancel
                    </SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Error Alert -->
                    <div v-if="form.errors.error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Error Creating Shipment
                                </h3>
                                <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    {{ form.errors.error }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- From Address -->
                    <AddressForm
                        :model-value="fromAddress"
                        @update:model-value="updateFromAddress"
                        prefix="from"
                        title="From Address (Sender)"
                        :errors="form.errors"
                    />

                    <!-- To Address -->
                    <AddressForm
                        :model-value="toAddress"
                        @update:model-value="updateToAddress"
                        prefix="to"
                        title="To Address (Recipient)"
                        :errors="form.errors"
                    />

                    <!-- Package Details -->
                    <PackageForm
                        :model-value="packageDetails"
                        @update:model-value="updatePackage"
                        :errors="form.errors"
                    />

                    <!-- Important Notice -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Important Notice
                                </h3>
                                <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    This will create and purchase a USPS shipping label. In test mode, no actual charges will be made.
                                    Please verify all addresses are correct before proceeding.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <Link :href="route('shipments.index')">
                            <SecondaryButton type="button">
                                Cancel
                            </SecondaryButton>
                        </Link>
                        
                        <PrimaryButton
                            :disabled="form.processing"
                            class="relative"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Creating Shipment...
                            </span>
                            <span v-else>
                                Create Shipment & Purchase Label
                            </span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

