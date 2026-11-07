<script setup>
import { ref, onMounted } from 'vue';
import api, { login } from './api';
import AlertMessage from './components/AlertMessage.vue';
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

async function loadStudents() {
    loading.value = true;
    try {
        const { data } = await api.get('/students');
        students.value = data.data;
    } catch (e) {
        notify('error', e.response?.data?.message ?? 'Could not load students.');
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
            notify('error', e.response?.data?.message ?? 'Something went wrong.');
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
        notify('error', e.response?.data?.message ?? 'Delete failed.');
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

onMounted(async () => {
    try {
        // Always get a fresh token so an old or invalid one can't cause 401
        await login('admin@example.com', 'AdminPass123!');
    } catch (e) {
        notify('error', e.response?.data?.message ?? 'Login failed.');
        return;
    }
    await loadStudents();
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-800">
        <div class="mx-auto max-w-4xl p-6">
            <h1 class="mb-6 text-2xl font-bold">Student Management</h1>

            <AlertMessage :type="alert.type" :message="alert.message" />

            <StudentForm
                :key="formKey"
                :student="editing"
                :errors="errors"
                :saving="saving"
                @save="saveStudent"
                @cancel="cancelEdit"
            />

            <StudentTable
                :students="students"
                :loading="loading"
                @edit="startEdit"
                @remove="removeStudent"
            />
        </div>
    </div>
</template>