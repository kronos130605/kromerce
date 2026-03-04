<template>
    <div class="no-business-role">
        <Head title="Access Denied" />

        <CustomerLayout>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Access Denied</h1>
                                    <p class="text-gray-600 dark:text-gray-400">Business Dashboard Access Required</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="px-8 py-6">
                            <div class="text-center py-8">
                                <div class="text-6xl mb-6">🔒</div>

                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                    You don't have access to the Business Dashboard
                                </h2>

                                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
                                    The Business Dashboard is only available to users with business roles.
                                    Your current account doesn't have the necessary permissions to access this area.
                                </p>

                                <!-- Current User Info -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-8 max-w-md mx-auto">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Current Account</h3>
                                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex justify-between">
                                            <span>Email:</span>
                                            <span class="font-mono">{{ user.email }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Name:</span>
                                            <span>{{ user.name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Role:</span>
                                            <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded text-xs font-medium">
                                                {{ userRole || 'No role assigned' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Options -->
                                <div class="space-y-4">
                                    <!-- Option 1: Create Business -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">
                                            Start Your Business
                                        </h3>
                                        <p class="text-blue-700 dark:text-blue-300 mb-4">
                                            Create a new business account to access the Business Dashboard
                                        </p>
                                        <button @click="createBusiness" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            Create Business Account
                                        </button>
                                    </div>

                                    <!-- Option 2: Join Existing Business -->
                                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                        <h3 class="text-lg font-medium text-green-900 dark:text-green-100 mb-2">
                                            Join Existing Business
                                        </h3>
                                        <p class="text-green-700 dark:text-green-300 mb-4">
                                            If you've been invited to join a business, check your email for the invitation
                                        </p>
                                        <button @click="checkInvitations" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                                            Check Invitations
                                        </button>
                                    </div>

                                    <!-- Option 3: Contact Support -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                            Need Help?
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                                            Contact our support team if you believe this is an error
                                        </p>
                                        <button @click="contactSupport" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                                            Contact Support
                                        </button>
                                    </div>
                                </div>

                                <!-- Back to Dashboard -->
                                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-600">
                                    <button @click="goToCustomerDashboard" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        ← Back to Customer Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CustomerLayout>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { Head } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)
const userRole = computed(() => page.props.user_role || null)

const createBusiness = () => {
    // TODO: Implementar cuando la ruta esté disponible
    console.log('Create business account - Route not implemented yet')
    // window.location.href = '/business/create'
}

const checkInvitations = () => {
    // TODO: Implementar cuando la ruta esté disponible
    console.log('Check invitations - Route not implemented yet')
    // window.location.href = '/invitations'
}

const contactSupport = () => {
    // TODO: Implementar cuando la ruta esté disponible
    console.log('Contact support - Route not implemented yet')
    // window.location.href = '/support'
}

const goToCustomerDashboard = () => {
    // TODO: Implementar cuando la ruta esté disponible
    console.log('Go to customer dashboard - Route not implemented yet')
    // window.location.href = '/dashboard/customer'
}
</script>

<style scoped>
.no-business-role {
    @apply min-h-screen;
}
</style>
