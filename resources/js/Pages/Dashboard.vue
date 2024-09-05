<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Icon} from '@iconify/vue';
import {ref, watch} from 'vue';
import {Head, Link, router, useForm} from '@inertiajs/vue3';
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    totalNoOfUsers: Number,
    totalNoOfEmailServed: Number,
    users: Object,
});

const logoUploadForm = useForm({
    logo: null,
});

const faviconUploadForm = useForm({
    favicon: null,
});

const logoFileInput = ref(null);
const faviconFileInput = ref(null);

const handleLogoFormSubmit = () => {
    logoUploadForm.logo = logoFileInput.value.files[0];
    logoUploadForm.post(route('general-setting.logo-upload'));
};

const handleFaviconFormSubmit = () => {
    faviconUploadForm.favicon = faviconFileInput.value.files[0];
    faviconUploadForm.post(route('general-setting.favicon-upload'));
};

const q = ref('');
const handleSearch = () => {
    router.visit(route('admin.get-admin-dashboard', {q: q.value}), {
        only: ['users'],
        preserveState: true,
    });
};

const handleClearSearch = () => {
    router.visit(route('admin.get-admin-dashboard'));
};

watch(q, () => {
    handleSearch();
})

</script>

<template>
    <Head title="Admin Dashboard"/>
    <AuthenticatedLayout>
        <div class="md:max-h-[calc(100vh-102px)] bg-gray-50 shadow-lg rounded-xl flex flex-col">
            <!-- Statistics Section -->
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 bg-white shadow-sm rounded-t-xl">
                <div class="flex items-center p-4 bg-gray-100 rounded-lg">
                    <Icon icon="mdi:account-multiple" class="text-slate-600 w-8 h-8"/>
                    <div class="ml-4">
                        <p class="text-gray-700 text-sm">Total Users</p>
                        <p class="text-xl font-semibold">{{ props.totalNoOfUsers }}</p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-gray-100 rounded-lg">
                    <Icon icon="mdi:email-outline" class="text-slate-600 w-8 h-8"/>
                    <div class="ml-4">
                        <p class="text-gray-700 text-sm">Emails Sent</p>
                        <p class="text-xl font-semibold">{{ props.totalNoOfEmailServed }}</p>
                    </div>
                </div>
                <!-- Add more statistic cards as needed -->
            </div>

            <!-- Forms Section -->
            <div class="p-4 shadow-sm flex flex-wrap space-y-5 md:space-y-0 md:space-x-10">
                <form @submit.prevent="handleLogoFormSubmit" class=" w-full sm:w-auto flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 sm:items-end">
                    <div class="">
                        <label for="logo" class="text-sm font-semibold text-gray-700 mb-2 inline-block">Upload Your
                            Logo</label>
                        <input type="file" ref="logoFileInput" id="logo"
                               class="block w-full md:w-auto border border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 sm:text-sm"/>
                        <InputError :message="logoUploadForm.errors.logo"/>
                    </div>
                    <button
                        type="submit"
                        class="py-2 px-4 text-[#444746] text-sm border border-[#747775] rounded-lg hover:bg-slate-900 hover:text-white transition-all duration-200">
                        Upload Logo
                    </button>
                </form>
                <form @submit.prevent="handleFaviconFormSubmit" class=" w-full sm:w-auto flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 sm:items-end">
                    <div class="">
                        <label for="favicon" class="text-sm font-semibold text-gray-700 mb-2 inline-block">Upload Your
                            Favicon</label>
                        <input type="file" ref="faviconFileInput" id="favicon"
                               class="block w-full md:w-auto border border-gray-300 shadow-sm focus:ring-slate-500 focus:border-slate-500 sm:text-sm"/>
                        <InputError :message="faviconUploadForm.errors.favicon"/>
                    </div>
                    <button
                        type="submit"
                        class="py-2 px-4 text-[#444746] text-sm border border-[#747775] rounded-lg hover:bg-slate-900 hover:text-white transition-all duration-200">
                        Upload Favicon
                    </button>
                </form>
            </div>

            <!-- Pagination and Reload Button -->
            <div class="flex-none flex flex-col sm:flex-row items-center justify-between bg-white p-4 shadow-sm space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="flex flex-col sm:flex-row gap-3 rounded-md sm:min-w-[350px]">
                    <input
                        type="text"
                        v-model="q"
                        placeholder="Search"
                        class="block w-full border-1 border-gray-300 shadow-sm focus:ring-0 focus:border-0 sm:text-sm"
                    />                    
                    <button
                        type="button"
                        @click="handleClearSearch"
                        class="py-2 px-6 border-none ring-0 outline-none shadow-sm text-sm font-medium text-gray-600 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-0 w-full sm:w-[200px]"
                    >
                        Clear Search
                    </button>
                </div>
                <div class="flex items-center space-x-5">
                    <div class="flex items-center gap-4">
                        <div class="relative group">
                            <Link
                                :href="route('admin.get-admin-dashboard')"
                                class="w-10 h-10 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full hover:bg-gray-200"
                            >
                                <Icon icon="tdesign:refresh"/>
                            </Link>
                            <span
                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            >Refresh
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-700 text-sm">
                            {{ `${props.users.from ?? 0} - ${props.users.to ?? 0} of ${props.users.total}` }}
                            </span>
                            <Link
                                class="text-gray-700 text-sm disabled:opacity-50"
                                :href="props.users.prev_page_url"
                                :disabled="!props.users.prev_page_url"
                            >
                                <Icon icon="ri:arrow-left-s-line"/>
                            </Link>
                            <Link
                                class="text-gray-700 text-sm disabled:opacity-50"
                                :href="props.users.next_page_url"
                                :disabled="!props.users.next_page_url"
                            >
                                <Icon icon="ri:arrow-right-s-line"/>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Table Section -->
            <div class="flex-auto overflow-y-auto max-h-[calc(100vh-260px)] p-4 bg-white rounded-b-xl">
                <table class="min-w-full bg-white">
                    <thead>
                    <tr class="w-full bg-gray-50">
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr v-for="(user, index) in props.users.data" :key="index" class="hover:bg-gray-50">
                        <td class="py-2 px-3 text-sm text-gray-700">{{ user.name }}</td>
                        <td class="py-2 px-3 text-sm text-gray-700">{{ user.email }}</td>
                        <td class="py-2 px-3 text-sm text-gray-700">{{ user.is_admin ? 'Admin' : 'Employee' }}</td>
                        <td class="py-2 px-3 text-sm">
                            <button
                                @click="router.post(route('admin.toggle-admin-status', user.id))"
                                class="text-slate-600 hover:text-slate-900"
                            >
                                Toggle Admin
                            </button>
                        </td>
                    </tr>
                    <tr v-if="props.users.data.length === 0" class="hover:bg-gray-50">
                        <td colspan="4" class="py-2 px-3 text-sm text-gray-700 text-center">
                            No data found!
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Add any additional custom styles here */
</style>
