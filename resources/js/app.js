import Alpine from 'alpinejs';
import 'animate.css';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Utility functions
window.showNotification = function(message, type = 'success') {
    const className = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 border rounded ${className}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
};

// API helper
window.api = {
    async get(url, options = {}) {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                ...options.headers,
            },
        });
        return response.json();
    },

    async post(url, data = {}, options = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                ...options.headers,
            },
            body: JSON.stringify(data),
        });
        return response.json();
    },
};

console.log('NEET LMS App initialized');
