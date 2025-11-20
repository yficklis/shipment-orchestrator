<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
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
            Package Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Weight -->
            <div class="md:col-span-2">
                <InputLabel for="weight" value="Weight (ounces) *" />
                <TextInput
                    id="weight"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="mt-1 block w-full"
                    :model-value="modelValue.weight"
                    @update:model-value="updateField('weight', $event)"
                    placeholder="16.0"
                    required
                />
                <InputError class="mt-2" :message="errors.weight" />
                <p class="mt-1 text-xs text-gray-500">
                    1 pound = 16 ounces. Example: 1.5 lbs = 24 oz
                </p>
            </div>

            <!-- Length -->
            <div>
                <InputLabel for="length" value="Length (inches)" />
                <TextInput
                    id="length"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="mt-1 block w-full"
                    :model-value="modelValue.length"
                    @update:model-value="updateField('length', $event)"
                    placeholder="12.0"
                />
                <InputError class="mt-2" :message="errors.length" />
            </div>

            <!-- Width -->
            <div>
                <InputLabel for="width" value="Width (inches)" />
                <TextInput
                    id="width"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="mt-1 block w-full"
                    :model-value="modelValue.width"
                    @update:model-value="updateField('width', $event)"
                    placeholder="9.0"
                />
                <InputError class="mt-2" :message="errors.width" />
            </div>

            <!-- Height -->
            <div class="md:col-span-2">
                <InputLabel for="height" value="Height (inches)" />
                <TextInput
                    id="height"
                    type="number"
                    step="0.1"
                    min="0.1"
                    class="mt-1 block w-full"
                    :model-value="modelValue.height"
                    @update:model-value="updateField('height', $event)"
                    placeholder="3.0"
                />
                <InputError class="mt-2" :message="errors.height" />
            </div>

            <!-- Info Box -->
            <div class="md:col-span-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Note:</strong> Weight is required. Dimensions are optional but recommended for accurate pricing.
                    All measurements should be in inches and ounces.
                </p>
            </div>
        </div>
    </div>
</template>

