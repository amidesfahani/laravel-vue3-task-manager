import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getTasks, createTask, updateTask, deleteTask } from '@/api';

export const useTaskStore = defineStore('tasks', () => {
	const tasks = ref([]);
	const meta = ref({});

	const fetchTasks = async (page = 1) => {
		try {
			const response = await getTasks(page);
			tasks.value = response.data.data || [];
			meta.value = {
				current_page: response.data.meta.current_page,
				last_page: response.data.meta.last_page,
				total: response.data.meta.total,
				per_page: response.data.meta.per_page,
			};
		} catch (error) {
			console.log(error);
			tasks.value = [];
		}
	};

	const addTask = async (task) => {
		const { data } = await createTask(task);
		tasks.value.push(data);
	};

	const editTask = async (id, task) => {
		const { data } = await updateTask(id, task);
		const index = tasks.value.findIndex((t) => t.id === id);
		if (index !== -1) tasks.value[index] = data;
	};

	const removeTask = async (id) => {
		await deleteTask(id);
		tasks.value = tasks.value.filter((t) => t.id !== id);
	};

	return { tasks, meta, fetchTasks, addTask, editTask, removeTask };
});