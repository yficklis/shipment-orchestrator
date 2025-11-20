<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    prefix: {
        type: String,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    title: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const updateField = (field, value) => {
    emit('update:modelValue', {
        ...props.modelValue,
        [field]: value,
    });
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
            {{ title }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Name -->
            <div class="md:col-span-2">
                <InputLabel :for="`${prefix}_name`" value="Full Name *" />
                <TextInput
                    :id="`${prefix}_name`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.name"
                    @update:model-value="updateField('name', $event)"
                    required
                />
                <InputError class="mt-2" :message="errors[`${prefix}_name`]" />
            </div>

            <!-- Street Address 1 -->
            <div class="md:col-span-2">
                <InputLabel :for="`${prefix}_street1`" value="Street Address *" />
                <TextInput
                    :id="`${prefix}_street1`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.street1"
                    @update:model-value="updateField('street1', $event)"
                    required
                />
                <InputError class="mt-2" :message="errors[`${prefix}_street1`]" />
            </div>

            <!-- Street Address 2 -->
            <div class="md:col-span-2">
                <InputLabel :for="`${prefix}_street2`" value="Apartment, Suite, etc. (Optional)" />
                <TextInput
                    :id="`${prefix}_street2`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.street2"
                    @update:model-value="updateField('street2', $event)"
                />
                <InputError class="mt-2" :message="errors[`${prefix}_street2`]" />
            </div>

            <!-- City -->
            <div>
                <InputLabel :for="`${prefix}_city`" value="City *" />
                <TextInput
                    :id="`${prefix}_city`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.city"
                    @update:model-value="updateField('city', $event)"
                    required
                />
                <InputError class="mt-2" :message="errors[`${prefix}_city`]" />
            </div>

            <!-- State -->
            <div>
                <InputLabel :for="`${prefix}_state`" value="State *" />
                <TextInput
                    :id="`${prefix}_state`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.state"
                    @update:model-value="updateField('state', $event)"
                    placeholder="CA"
                    maxlength="2"
                    required
                />
                <InputError class="mt-2" :message="errors[`${prefix}_state`]" />
                <p class="mt-1 text-xs text-gray-500">2-letter state code (e.g., CA, NY)</p>
            </div>

            <!-- ZIP Code -->
            <div>
                <InputLabel :for="`${prefix}_zip`" value="ZIP Code *" />
                <TextInput
                    :id="`${prefix}_zip`"
                    type="text"
                    class="mt-1 block w-full"
                    :model-value="modelValue.zip"
                    @update:model-value="updateField('zip', $event)"
                    placeholder="12345"
                    required
                />
                <InputError class="mt-2" :message="errors[`${prefix}_zip`]" />
            </div>

            <!-- Phone (Optional) -->
            <div>
                <InputLabel :for="`${prefix}_phone`" value="Phone (Optional)" />
                <TextInput
                    :id="`${prefix}_phone`"
                    type="tel"
                    class="mt-1 block w-full"
                    :model-value="modelValue.phone"
                    @update:model-value="updateField('phone', $event)"
                />
                <InputError class="mt-2" :message="errors[`${prefix}_phone`]" />
            </div>

            <!-- Email (Optional) -->
            <div class="md:col-span-2">
                <InputLabel :for="`${prefix}_email`" value="Email (Optional)" />
                <TextInput
                    :id="`${prefix}_email`"
                    type="email"
                    class="mt-1 block w-full"
                    :model-value="modelValue.email"
                    @update:model-value="updateField('email', $event)"
                />
                <InputError class="mt-2" :message="errors[`${prefix}_email`]" />
            </div>
        </div>
    </div>
</template>

