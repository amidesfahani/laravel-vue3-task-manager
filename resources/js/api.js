import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
});

export const setAuthToken = (token) => {
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
};

export const login = (credentials) => api.post('/login', credentials);
export const register = (data) => api.post('/register', data);
export const logout = () => api.post('/logout');
export const getTasks = (params) => api.get('/tasks', { params });
export const createTask = (task) => api.post('/tasks', task);
export const updateTask = (id, task) => api.put(`/tasks/${id}`, task);
export const deleteTask = (id) => api.delete(`/tasks/${id}`);