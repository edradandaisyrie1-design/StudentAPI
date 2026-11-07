import { reactive } from 'vue';
import api from '../api';

function readUser() {
    try {
        return JSON.parse(localStorage.getItem('user'));
    } catch {
        return null;
    }
}

export const auth = reactive({
    token: localStorage.getItem('token'),
    user: readUser(),
    get isLoggedIn() {
        return !!this.token;
    },
    get isAdmin() {
        return this.user?.role === 'admin';
    },
});

export async function login(email, password) {
    const { data } = await api.post('/login', { email, password });

    auth.token = data.token;
    auth.user = data.user;
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
}

export function clearSession() {
    auth.token = null;
    auth.user = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
}

export async function logout() {
    try {
        await api.post('/logout');
    } catch {
        // The token may already be invalid. We still clear the session below.
    }

    clearSession();
}