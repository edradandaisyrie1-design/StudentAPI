<script setup>
import { ref, onMounted } from 'vue';
import api from './api';
import { auth, logout, clearSession } from './store/auth';
import AlertMessage from './components/AlertMessage.vue';
import LoginForm from './components/LoginForm.vue';
import StudentForm from './components/StudentForm.vue';
import StudentTable from './components/StudentTable.vue';

const students = ref([]);
const editing = ref(null);
const errors = ref({});
const loading = ref(false);
const saving = ref(false);
const formKey = ref(0);
const alert = ref({ type: 'success', message: '' });

let timer;
function notify(type, message) {
    alert.value = { type, message };
    clearTimeout(timer);
    timer = setTimeout(() => (alert.value.message = ''), 4000);
}

function handleError(e, fallback) {
    if (e.response?.status === 401) {
        clearSession();
        students.value = [];
        notify('error', 'Your session expired. Please log in again.');
        return;
    }
    notify('error', e.response?.data?.message ?? fallback);
}

async function loadStudents() {
    loading.value = true;
    try {
        const { data } = await api.get('/students');
        students.value = data.data;
    } catch (e) {
        handleError(e, 'Could not load students.');
    } finally {
        loading.value = false;
    }
}

async function saveStudent(payload) {
    saving.value = true;
    errors.value = {};
    try {
        const { data } = editing.value
            ? await api.put(`/students/${editing.value.id}`, payload)
            : await api.post('/students', payload);

        notify('success', data.message);
        editing.value = null;
        formKey.value++;
        await loadStudents();
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors;
            notify('error', 'Please fix the highlighted fields.');
        } else {
            handleError(e, 'Something went wrong.');
        }
    } finally {
        saving.value = false;
    }
}

async function removeStudent(student) {
    if (!confirm(`Delete ${student.name}?`)) return;
    try {
        const { data } = await api.delete(`/students/${student.id}`);
        notify('success', data.message);
        await loadStudents();
    } catch (e) {
        handleError(e, 'Delete failed.');
    }
}

function startEdit(student) {
    editing.value = student;
    errors.value = {};
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit() {
    editing.value = null;
    errors.value = {};
}

async function onLoggedIn() {
    notify('success', `Welcome, ${auth.user.name}!`);
    await loadStudents();
}

async function doLogout() {
    await logout();
    students.value = [];
    editing.value = null;
    notify('success', 'You have been logged out.');
}

onMounted(async () => {
    if (!auth.isLoggedIn) return;
    try {
        const { data } = await api.get('/me');
        auth.user = data.user;
        localStorage.setItem('user', JSON.stringify(data.user));
        await loadStudents();
    } catch (e) {
        handleError(e, 'Could not verify your session.');
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-800">
        <div class="mx-auto max-w-4xl p-6">
            <header class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Student Management</h1>

                <div v-if="auth.isLoggedIn" class="flex items-center gap-3 text-sm">
                    <span>{{ auth.user?.name }}</span>
                    <span
                        class="rounded-full px-2 py-0.5 text-xs"
                        :class="auth.isAdmin ? 'bg-purple-100 text-purple-800' : 'bg-gray-200 text-gray-700'"
                    >
                        {{ auth.isAdmin ? 'Administrator' : 'Regular User' }}
                    </span>
                    <button @click="doLogout" class="rounded border px-3 py-1">Logout</button>
                </div>
            </header>

            <AlertMessage :type="alert.type" :message="alert.message" />

            <LoginForm v-if="!auth.isLoggedIn" @success="onLoggedIn" />

            <template v-else>
                <StudentForm
                    v-if="auth.isAdmin"
                    :key="formKey"
                    :student="editing"
                    :errors="errors"
                    :saving="saving"
                    @save="saveStudent"
                    @cancel="cancelEdit"
                />
                <p v-else class="mb-6 rounded border border-blue-200 bg-blue-50 px-4 py-2 text-sm text-blue-800">
                    You are logged in as a Regular User. You can view students but cannot add, edit or delete them.
                </p>

                <StudentTable
                    :students="students"
                    :loading="loading"
                    :can-manage="auth.isAdmin"
                    @edit="startEdit"
                    @remove="removeStudent"
                />
            </template>
        </div>
    </div>
</template>