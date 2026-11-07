<script setup>
import { ref } from 'vue';
import { login } from '../store/auth';
import AlertMessage from './AlertMessage.vue';

const emit = defineEmits(['success']);

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const fieldErrors = ref({});

async function submit() {
    loading.value = true;
    error.value = '';
    fieldErrors.value = {};

    try {
        await login(email.value, password.value);
        emit('success');
    } catch (e) {
        if (e.response?.status === 422) {
            fieldErrors.value = e.response.data.errors;
        } else {
            error.value = e.response?.data?.message ?? 'Unable to log in. Is the server running?';
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto max-w-sm space-y-4 rounded border bg-white p-6">
        <h2 class="text-lg font-semibold">Log in</h2>

        <AlertMessage type="error" :message="error" />

        <label class="block text-sm">
            Email
            <input v-model="email" type="email" autocomplete="username" class="mt-1 w-full rounded border px-3 py-2" />
            <span v-if="fieldErrors.email" class="text-red-600">{{ fieldErrors.email[0] }}</span>
        </label>

        <label class="block text-sm">
            Password
            <input v-model="password" type="password" autocomplete="current-password" class="mt-1 w-full rounded border px-3 py-2" />
            <span v-if="fieldErrors.password" class="text-red-600">{{ fieldErrors.password[0] }}</span>
        </label>

        <button type="submit" :disabled="loading" class="w-full rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50">
            {{ loading ? 'Logging in...' : 'Log in' }}
        </button>
    </form>
</template>