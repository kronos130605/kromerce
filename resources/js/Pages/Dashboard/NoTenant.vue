<template>
    <div class="no-tenant">
        <div class="no-tenant-container">
            <div class="icon-container">
                <svg class="w-24 h-24 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            
            <div class="content">
                <h1 class="title">No Business Account</h1>
                <p class="message">
                    Your account is not associated with a business yet. 
                    Please create or join a business to access the dashboard.
                </p>
                
                <div class="actions">
                    <button @click="createBusiness" class="btn btn-primary">
                        Create New Business
                    </button>
                    
                    <button @click="joinBusiness" class="btn btn-secondary">
                        Join Existing Business
                    </button>
                    
                    <Link href="/home" class="btn btn-outline">
                        Back to Home
                    </Link>
                </div>
                
                <div class="help-text">
                    <p>Need help? <a href="/support" class="help-link">Contact Support</a></p>
                </div>
            </div>
        </div>
        
        <div class="user-info">
            <div class="user-card">
                <div class="user-avatar">
                    <img v-if="auth.user.avatar_url" :src="auth.user.avatar_url" :alt="auth.user.name" />
                    <div v-else class="avatar-placeholder">
                        {{ auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
                <div class="user-details">
                    <h3>{{ auth.user.name }}</h3>
                    <p>{{ auth.user.email }}</p>
                    <span class="user-status">Individual Account</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    auth: Object,
})

const createBusiness = () => {
    // Navigate to business creation page
    window.location.href = '/business/create'
}

const joinBusiness = () => {
    // Navigate to business join page
    window.location.href = '/business/join'
}
</script>

<style scoped>
.no-tenant {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.no-tenant-container {
    background: white;
    border-radius: 1rem;
    padding: 3rem;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-width: 600px;
    margin-bottom: 2rem;
}

.icon-container {
    margin-bottom: 2rem;
}

.title {
    font-size: 2.5rem;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 1rem;
}

.message {
    color: #6b7280;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}

.btn {
    padding: 0.875rem 2rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 1rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f9fafb;
    transform: translateY(-1px);
}

.help-text {
    color: #9ca3af;
    font-size: 0.875rem;
}

.help-link {
    color: #3b82f6;
    text-decoration: none;
}

.help-link:hover {
    text-decoration: underline;
}

.user-info {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
}

.user-card {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.25rem;
}

.user-details {
    text-align: left;
    color: white;
}

.user-details h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.user-details p {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.user-status {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

@media (max-width: 640px) {
    .no-tenant-container {
        padding: 2rem;
        margin: 1rem;
    }
    
    .title {
        font-size: 2rem;
    }
    
    .actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 300px;
    }
    
    .user-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>
