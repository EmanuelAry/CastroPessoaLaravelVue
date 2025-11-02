<template>
    <select
        :value="modelValue"
        @change="onChange"
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
    >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <slot />
    </select>
</template>

<script setup lang="ts">
defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

// defineEmits<{
//     'update:modelValue': [value: string];
// }>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const onChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    emit('update:modelValue', target.value);
};
</script>