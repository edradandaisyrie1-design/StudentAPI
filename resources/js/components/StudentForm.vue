<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
    student: { type: Object, default: null },
    errors: { type: Object, default: () => ({}) },
    saving: { type: Boolean, default: false },
});

const emit = defineEmits(['save', 'cancel']);

const fields = [
    { key: 'student_number', label: 'Student Number', type: 'text' },
    { key: 'name', label: 'Full Name', type: 'text' },
    { key: 'email', label: 'Email', type: 'email' },
    { key: 'course', label: 'Course', type: 'text' },
];

const blank = () => ({
    student_number: '',
    name: '',
    email: '',
    course: '',
    year_level: 1,
});

const form = reactive(blank());

watch(
    () => props.student,
    (student) => {
        Object.assign(form, student ? { ...student } : blank());
    },
    { immediate: true },
);

function submit() {
    emit('save', {
        student_number: form.student_number,
        name: form.name,
        email: form.email,
        course: form.course,
        year_level: form.year_level,
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="mb-6 grid gap-4 rounded border bg-white p-4 md:grid-cols-2">
        <h2 class="text-lg font-semibold md:col-span-2">
            {{ student ? 'Edit Student' : 'Add Student' }}
        </h2>

        <label v-for="field in fields" :key="field.key" class="block text-sm">
            {{ field.label }}
            <input
                v-model="form[field.key]"
                :type="field.type"
                class="mt-1 w-full rounded border px-3 py-2"
            />
            <span v-if="errors[field.key]" class="text-red-600">{{ errors[field.key][0] }}</span>
        </label>

        <label class="block text-sm">
            Year Level
            <select v-model.number="form.year_level" class="mt-1 w-full rounded border px-3 py-2">
                <option v-for="n in 4" :key="n" :value="n">Year {{ n }}</option>
            </select>
            <span v-if="errors.year_level" class="text-red-600">{{ errors.year_level[0] }}</span>
        </label>

        <div class="flex gap-2 md:col-span-2">
            <button type="submit" :disabled="saving" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50">
                {{ saving ? 'Saving...' : student ? 'Update Student' : 'Add Student' }}
            </button>
            <button v-if="student" type="button" @click="emit('cancel')" class="rounded border px-4 py-2">
                Cancel
            </button>
        </div>
    </form>
</template>