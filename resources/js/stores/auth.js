import { defineStore } from 'pinia';
import { ref } from 'vue';
import { login, register, logout, setAuthToken } from '@/api';

export const useAuthStore = defineStore('auth', () => {
	const token = ref(localStorage.getItem('token') || null);
	const user = ref(null);

	const setToken = (newToken) => {
		token.value = newToken;
		localStorage.setItem('token', newToken);
		setAuthToken(newToken);
	};

	const loginUser = async (credentials) => {
		const { data } = await login(credentials);
		setToken(data.token);
		user.value = data.user;
	};

	const registerUser = async (data) => {
		const { data: response } = await register(data);
		setToken(response.token);
		user.value = response.user;
	};

	const logoutUser = async () => {
		await logout();
		token.value = null;
		localStorage.removeItem('token');
		setAuthToken(null);
		user.value = null;
	};

	return { token, user, loginUser, registerUser, logoutUser, setToken };
});