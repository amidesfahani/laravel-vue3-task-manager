<template>
    <div
        class="flex bg-gray-100 items-center justify-center w-full min-h-screen transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <main class="flex max-w-[335px] w-full flex-col-reverse lg:flex-row">
            <div class="w-full p-6 bg-white rounded-lg shadow-lg">
                <h2 class="mb-6 text-2xl font-bold text-center text-gray-800">ورود به سیستم</h2>
                <form @submit.prevent="login" class="space-y-4">
                    <div v-if="errorMessage" class="p-3 text-sm text-red-700 bg-red-100 rounded">
                        {{ errorMessage }}
                    </div>

                    <div>
                        <input v-model="form.email" type="email" placeholder="ایمیل" required
                            class="w-full p-3 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 ltr"
                            :class="{ 'border-red-500': errors.email }" />
                        <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                    </div>

                    <div>
                        <input v-model="form.password" type="password" placeholder="رمز عبور" required
                            class="w-full p-3 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 ltr"
                            :class="{ 'border-red-500': errors.password }" />
                        <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password[0] }}</p>
                    </div>

                    <button type="submit"
                        class="cursor-pointer w-full p-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        ورود
                    </button>

                    <button type="button" @click="goToRegister"
                        class="cursor-pointer w-full p-3 text-blue-600 bg-transparent border border-blue-600 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        ثبت‌نام
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const form = reactive({ email: '', password: '' });
const errors = ref({});
const errorMessage = ref('');

const login = async () => {
    try {
        errors.value = {};
        errorMessage.value = '';
        await authStore.loginUser(form);
        router.push('/dashboard');
    } catch (error) {
        if (error.response && error.response.data) {
            const response = error.response.data;
            if (response.errors) {
                errors.value = response.errors;
            } else if (response.message) {
                errorMessage.value = response.message;
            }
        } else {
            errorMessage.value = 'خطایی رخ داده است. لطفاً دوباره تلاش کنید.';
        }
        console.error('ورود ناموفق', error);
    }
};

const goToRegister = () => {
    router.push('/register');
};
</script>