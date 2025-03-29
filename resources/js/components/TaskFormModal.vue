<template>
	<div v-if="isOpen" class="fixed inset-0 bg-black/50 flex justify-center items-center">
		<div class="bg-white p-6 rounded-lg w-full max-w-lg">
			<h3 class="text-xl font-bold mb-4">{{ taskToEdit ? 'ویرایش وظیفه' : 'افزودن وظیفه' }}</h3>

			<div v-if="generalError" class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded">
				{{ generalError }}
			</div>

			<form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
				<div class="flex flex-col">
					<input v-model="form.title" placeholder="عنوان" required
						class="p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
					<p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title[0] }}</p>
				</div>

				<div class="flex flex-col">
					<textarea v-model="form.description" placeholder="توضیحات (اختیاری)"
						class="p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
					<p v-if="errors.description" class="mt-1 text-xs text-red-500">{{ errors.description[0] }}</p>
				</div>

				<div class="flex flex-col">
					<select v-model="form.status"
						class="p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
						<option value="pending">در حال انجام</option>
						<option value="completed">انجام شده</option>
					</select>
					<p v-if="errors.status" class="mt-1 text-xs text-red-500">{{ errors.status[0] }}</p>
				</div>

				<div class="flex flex-col">
					<input
						v-model="form.end_date"
						placeholder="تاریخ شروع"
						class="p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
						type="text" id="form_start_date"
					/>
					<DatePicker
						v-model="form.start_date"
						required
						input-format="YYYY/MM/DD"
						format="YYYY/MM/DD"
						display-format="jYYYY/jMM/jDD"
						custom-input="#form_start_date"
					/>
					<p v-if="errors.start_date" class="mt-1 text-xs text-red-500">{{ errors.start_date[0] }}</p>
				</div>

				<div class="flex flex-col">
					<input
						v-model="form.end_date"
						placeholder="تاریخ پایان (اختیاری)"
						class="p-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
						type="text" id="form_end_date"
					/>
					<DatePicker
						v-model="form.end_date"
						input-format="YYYY/MM/DD"
						format="YYYY/MM/DD"
						display-format="jYYYY/jMM/jDD"
						custom-input="#form_end_date"
					/>
					<p v-if="errors.end_date" class="mt-1 text-xs text-red-500">{{ errors.end_date[0] }}</p>
				</div>

				<div class="flex justify-end gap-2">
					<button type="button" @click="$emit('close')"
						class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 cursor-pointer">
						لغو
					</button>
					<button type="submit"
						class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer">
						ذخیره
					</button>
				</div>
			</form>
		</div>
	</div>
</template>

<script setup>
import { ref, watch } from 'vue';
import DatePicker from 'vue3-persian-datetime-picker';
import { DateTime } from 'luxon';

const props = defineProps({
	isOpen: Boolean,
	taskToEdit: Object,
});
const emit = defineEmits(['close', 'save']);

const form = ref({
	id: null,
	title: '',
	description: '',
	status: 'pending',
	start_date: '',
	end_date: '',
});

const errors = ref({});
const generalError = ref('');

watch(
	() => props.taskToEdit,
	(newTask) => {
		if (newTask) {
			form.value = {
				id: newTask.id,
				title: newTask.title,
				description: newTask.description || '',
				status: newTask.status,
				start_date: DateTime.fromISO(newTask.start_date)
					.setZone('Asia/Tehran')
					.reconfigure({ calendar: 'persian' })
					.toFormat('yyyy/MM/dd'),
				end_date: newTask.end_date
					? DateTime.fromISO(newTask.end_date)
						.setZone('Asia/Tehran')
						.reconfigure({ calendar: 'persian' })
						.toFormat('yyyy/MM/dd')
					: '',
			};
		} else {
			form.value = { id: null, title: '', description: '', status: 'pending', start_date: '', end_date: '' };
		}

		errors.value = {};
		generalError.value = '';
	}
);

const handleSubmit = () => {
	const startDate = DateTime.fromFormat(form.value.start_date, 'yyyy/MM/dd', {
		zone: 'Asia/Tehran',
		calendar: 'persian',
	}).toISO();

	const endDate = form.value.end_date
		? DateTime.fromFormat(form.value.end_date, 'yyyy/MM/dd', {
			zone: 'Asia/Tehran',
			calendar: 'persian',
		}).toISO()
		: null;

	if (!startDate) {
		errors.value.start_date = ['تاریخ شروع نامعتبر است'];
		return;
	}
	if (form.value.end_date && !endDate) {
		errors.value.end_date = ['تاریخ پایان نامعتبر است'];
		return;
	}

	const taskData = {
		id: form.value.id,
		title: form.value.title,
		description: form.value.description || null,
		status: form.value.status,
		start_date: startDate,
		end_date: endDate,
	};

	try {
		emit('save', taskData);
	} catch (error) {
		if (error.response && error.response.data) {
			const { message, errors: apiErrors } = error.response.data;
			generalError.value = message || 'خطایی رخ داده است';
			if (apiErrors) {
				errors.value = apiErrors;
			}
		} else {
			generalError.value = 'خطای ناشناخته‌ای رخ داده است';
		}
	}
};
</script>