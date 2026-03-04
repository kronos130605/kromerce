<template>
    <div class="dashboard-error">
        <div class="error-container">
            <div class="error-icon">
                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <div class="error-content">
                <h1 class="error-title">Dashboard Error</h1>
                <p class="error-message">{{ error }}</p>

                <div v-if="details" class="error-details">
                    <h3>Technical Details:</h3>
                    <pre>{{ details }}</pre>
                </div>

                <div class="error-actions">
                    <button @click="refresh" class="btn btn-primary">
                        Refresh Dashboard
                    </button>

                    <Link href="/home" class="btn btn-secondary">
                        Go to Home
                    </Link>

                    <button @click="contactSupport" class="btn btn-outline">
                        Contact Support
                    </button>
                </div>
            </div>
        </div>

        <div class="user-info">
            <p><strong>User:</strong> {{ auth.user.name }}</p>
            <p><strong>Email:</strong> {{ auth.user.email }}</p>
            <p v-if="tenant"><strong>Tenant ID:</strong> {{ tenant.id }}</p>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    error: String,
    details: String,
    auth: Object,
    tenant: Object,
})

const refresh = () => {
    window.location.reload()
}

const contactSupport = () => {
    window.location.href = 'mailto:support@kromerce.com?subject=Dashboard Error&body=' + encodeURIComponent(
        `Error: ${props.error}\n` +
        `Details: ${props.details || 'N/A'}\n` +
        `User: ${props.auth.user.name} (${props.auth.user.email})\n` +
        `Tenant ID: ${props.tenant?.id || 'N/A'}\n` +
        `Timestamp: ${new Date().toISOString()}`
    )
}
</script>

<style scoped>
.dashboard-error {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.error-container {
    background: white;
    border-radius: 1rem;
    padding: 3rem;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-width: 600px;
    margin-bottom: 2rem;
}

.error-icon {
    margin-bottom: 1.5rem;
}

.error-title {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 1rem;
}

.error-message {
    color: #6b7280;
    font-size: 1.1rem;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-details {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 2rem;
    text-align: left;
}

.error-details h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.error-details pre {
    font-size: 0.875rem;
    color: #6b7280;
    white-space: pre-wrap;
    word-break: break-word;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
}

.user-info {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
    color: white;
    font-size: 0.875rem;
}

.user-info p {
    margin: 0.25rem 0;
}

@media (max-width: 640px) {
    .error-container {
        padding: 2rem;
        margin: 1rem;
    }

    .error-actions {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>
