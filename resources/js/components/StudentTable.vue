<script setup>
defineProps({
    students: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    canManage: { type: Boolean, default: true },
});

const emit = defineEmits(['edit', 'remove']);
</script>

<template>
    <div class="overflow-x-auto rounded border bg-white">
        <p v-if="loading" class="p-4 text-gray-500">Loading students...</p>
        <p v-else-if="students.length === 0" class="p-4 text-gray-500">No students found.</p>

        <table v-else class="w-full text-left text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">Student No.</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Course</th>
                    <th class="p-3">Year</th>
                    <th v-if="canManage" class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="student in students" :key="student.id" class="border-t">
                    <td class="p-3">{{ student.student_number }}</td>
                    <td class="p-3">{{ student.name }}</td>
                    <td class="p-3">{{ student.email }}</td>
                    <td class="p-3">{{ student.course }}</td>
                    <td class="p-3">{{ student.year_level }}</td>
                    <td v-if="canManage" class="space-x-2 p-3">
                        <button @click="emit('edit', student)" class="rounded bg-amber-500 px-3 py-1 text-white">Edit</button>
                        <button @click="emit('remove', student)" class="rounded bg-red-600 px-3 py-1 text-white">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>