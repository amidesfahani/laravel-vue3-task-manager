<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white text-gray-800 p-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-xl font-bold">مدیریت وظایف</h1>
                <button @click="logout"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 cursor-pointer">
                    خروج
                </button>
            </div>
        </header>

        <main class="container mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">وظایف من</h2>
                <button @click="openCreateModal"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 cursor-pointer">
                    افزودن وظیفه
                </button>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">فیلتر وضعیت</label>
                    <select v-model="statusFilter" id="status-filter"
                        class="w-full p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">همه</option>
                        <option value="pending">در حال انجام</option>
                        <option value="completed">انجام شده</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label for="sort-filter" class="block text-sm font-medium text-gray-700 mb-1">مرتب‌سازی</label>
                    <select v-model="sortOrder" id="sort-filter"
                        class="w-full p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="newest">جدیدترین</option>
                        <option value="oldest">قدیمی‌ترین</option>
                    </select>
                </div>
            </div>

            <ul class="space-y-4">
                <li v-for="task in filteredTasks" :key="task.id"
                    class="p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-700 font-semibold">{{ task.title }}</span>
                            <p v-if="task.description" class="text-sm text-gray-500 mt-1">{{ task.description }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                شروع: {{ formatDate(task.start_date) }}
                                <span v-if="task.end_date"> - پایان: {{ formatDate(task.end_date) }}</span>
                            </p>
                            <p class="text-sm mt-1"
                                :class="task.status === 'completed' ? 'text-green-500' : 'text-yellow-500'">
                                وضعیت: {{ task.status === 'completed' ? 'انجام شده' : 'در حال انجام' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openEditModal(task)"
                                class="cursor-pointer px-4 py-2 text-sm text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                ویرایش
                            </button>
                            <button @click="confirmDelete(task.id)"
                                class="cursor-pointer px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400">
                                حذف
                            </button>
                        </div>
                    </div>
                </li>
                <li v-if="filteredTasks.length === 0" class="text-center text-gray-500 py-4">
                    وظیفه‌ای برای نمایش وجود ندارد.
                </li>
            </ul>

            <div class="mt-6 flex justify-center gap-4">
                <button @click="fetchPage(taskStore.meta.current_page - 1)"
                    :disabled="taskStore.meta.current_page === 1" class="px-4 py-2 border rounded-lg"
                    :class="taskStore.meta.current_page !== 1 ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'cursor-not-allowed opacity-50'">
                    قبلی
                </button>
                <button @click="fetchPage(taskStore.meta.current_page + 1)"
                    :disabled="taskStore.meta.current_page === taskStore.meta.last_page"
                    class="px-4 py-2 border rounded-lg"
                    :class="taskStore.meta.current_page !== taskStore.meta.last_page ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'cursor-not-allowed opacity-50'">
                    بعدی
                </button>
            </div>
        </main>

        <TaskFormModal :is-open="isModalOpen" :task-to-edit="taskToEdit" @close="closeModal" @save="saveTask" />
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useTaskStore } from '@/stores/tasks';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import { DateTime } from 'luxon';
import TaskFormModal from './TaskFormModal.vue';

const taskStore = useTaskStore();
const authStore = useAuthStore();
const router = useRouter();
const isModalOpen = ref(false);
const taskToEdit = ref(null);
const statusFilter = ref('all');
const sortOrder = ref('newest')

onMounted(async () => await taskStore.fetchTasks(1));

// const computedTasks = computed(() => taskStore.tasks);
const filteredTasks = computed(() => {
    let tasks = [...taskStore.tasks];

    if (statusFilter.value !== 'all') {
        tasks = tasks.filter(task => task.status === statusFilter.value);
    }

    tasks.sort((a, b) => {
        const dateA = DateTime.fromISO(a.start_date);
        const dateB = DateTime.fromISO(b.start_date);
        return sortOrder.value === 'newest' ? dateB - dateA : dateA - dateB;
    });

    return tasks;
});

const openCreateModal = () => {
    taskToEdit.value = null;
    isModalOpen.value = true;
};

const openEditModal = (task) => {
    taskToEdit.value = { ...task };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    taskToEdit.value = null;
};

const saveTask = async (task) => {
    if (task.id) {
        await taskStore.editTask(task.id, task);
    } else {
        await taskStore.addTask(task);
    }
    closeModal();
    taskStore.fetchTasks(1);
};

const confirmDelete = async (id) => {
    if (confirm('آیا مطمئن هستید که می‌خواهید این وظیفه را حذف کنید؟')) {
        await taskStore.removeTask(id);
        taskStore.fetchTasks(1);
    }
};

const logout = async () => {
    try {
        await authStore.logoutUser();
        router.push('/login');
    } catch (error) {
        console.error('Logout failed', error);
    }
};

const formatDate = (isoDate) => {
    return DateTime.fromISO(isoDate)
        .setZone('Asia/Tehran')
        .reconfigure({ outputCalendar: 'persian', locale: 'fa' })
        .toFormat('yyyy/MM/dd');
};

const fetchPage = (page) => {
    taskStore.fetchTasks(page);
};
</script>