<script setup>
defineProps({
    type: { type: String, default: 'success' },
    message: { type: String, default: '' },
});
</script>

<template>
    <div
        v-if="message"
        role="alert"
        class="mb-4 rounded border px-4 py-2 text-sm"
        :class="type === 'error'
            ? 'border-red-300 bg-red-100 text-red-800'
            : 'border-green-300 bg-green-100 text-green-800'"
    >
        {{ message }}
    </div>
</template>