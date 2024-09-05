<script setup>
import { ref, watch, onUnmounted , onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import SearchComponent from '../Components/Search.vue';
import ComposeMessage from '../Components/ComposeMessage.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import UserDummyImage from "@/Components/UserDummyImage.vue";

const isComposeModalShow = ref(false);

const toggleComposeMessage = () => {
    isComposeModalShow.value = true;
};
// open close area
// State to control visibility
const isVisible = ref(window.innerWidth >= 780);

// State to check screen size
const isSmallScreen = ref(window.innerWidth < 780);

// Handle screen resize
const handleResize = () => {
  const isCurrentlySmallScreen = window.innerWidth < 768;
    isSmallScreen.value = isCurrentlySmallScreen;
  console.log(isCurrentlySmallScreen , window.innerWidth);
  // Update visibility based on screen size
  if (isCurrentlySmallScreen) {
    isVisible.value = false;
  } else if (isVisible.value) {
    isVisible.value = true;
  }
};

// Initialize event listener
onMounted(() => {
  window.addEventListener('resize', handleResize);
  handleResize(); // Check screen size on mount
});

// Clean up event listener
onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
});

// Methods to control visibility
const close = () => {
    isVisible.value = false;
};

const open = () => {
    isVisible.value = true;
};
const items = ref([
    { text: 'Inbox', icon: 'tabler:mail', routeName: 'inbox' },
    { text: 'Sent', icon: 'hugeicons:sent', routeName: 'sent' },
    { text: 'Starred', icon: 'mingcute:star-line', routeName: 'starred' },
    { text: 'Important', icon: 'material-symbols:label-important-outline', routeName: 'important' },
    { text: 'Trash', icon: 'tabler:trash', routeName: 'trash' },
]);


const page = usePage()

watch(() => page.props.flash.message, (value) => {
    if (value) {
        if (page.props.flash.type === 'success') {
            toast(value, {
                "type": "success",
                "transition": "zoom",
                "dangerouslyHTMLString": true
            })
        } else {
            toast(value, {
                "type": "error",
                "transition": "zoom",
                "dangerouslyHTMLString": true
            })
        }
    }
}, { deep: true });

// profile image upload 
const showPopup = ref(false);
const popupModal = ref(null);

const togglePopup = () => {
    showPopup.value = !showPopup.value;
};

const showSection = ref(false);

const toggleSection = () => {
  showSection.value = !showSection.value;
};

const closetoggleSection = () => {
  showSection.value = !showSection.value;
};


</script>

<template>
    <div>
        <div class="min-h-screen bg-[#F6F8FC] flex relative">
            <!-- Sidebar Area -->
            <div v-if="isVisible"
                class="xl:w-[250px] shrink-0 min-h-screen md:bg-transparent md:relative absolute bg-white w-[280px] z-50">
                <div class="w-full h-[80px] flex items-center justify-between py-3 px-4">
                    <img :src="$page.props.settings.logo" alt="logo" width="100px" height="100px" />
                    <button @click="close" class="md:hidden">
                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div>
                    <div class="h-[calc(100vh-80px)] bg-transparent py-5 px-3">
                        <button @click="toggleComposeMessage"
                            class="w-full flex items-center justify-center font-medium px-3 py-3 bg-[#C2E7FF] text-[#2B4A62] rounded-full mb-5 gap-2 hover:shadow-lg transition-all duration-500">
                            Compose
                            <Icon icon="octicon:pencil-24" />
                        </button>
                        <ul>
                            <li v-for="(item, index) in items" :key="index">
                                <Link :href="route(item.routeName)" :class="[
                'flex items-center gap-x-2 text-base text-slate-800 rounded-md py-2 px-3 transition-all duration-500 mt-[2px]',
                { 'bg-[#D3E3FD] font-medium': route().current() === item.routeName, 'hover:bg-[#eeeeee]': route().current() !== item.routeName }
            ]">
                                <Icon class="text-slate-950 text-lg" :icon="item.icon" />
                                {{ item.text }}
                                </Link>
                            </li>
                            <li v-if="$page.props.auth.user.is_admin" class="absolute bottom-4">
                                <Link :href="route('admin.get-admin-dashboard')" :class="[
                'flex items-center gap-x-2 text-base text-slate-800 rounded-md py-2 px-3 transition-all duration-500 mt-[2px]',
                { 'bg-slate-200 font-medium': route().current() === 'admin.get-admin-dashboard', 'hover:bg-slate-100': route().current() !== 'admin.get-admin-dashboard' }
            ]">
                                <Icon class="text-slate-950 text-lg" :icon="'material-symbols:dashboard-outline'" />
                                {{ 'Dashboard' }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="max-w-[full] overflow-hidden">
                <!-- Top bar Area -->
                <div
                    class="bg-transparent md:h-[81px] xl:w-[calc(100vw-250px)] px-5 py-3 flex items-center justify-between gap-3">
                    <button @click="open" class="md:hidden"><Icon icon="mingcute:menu-fill" /></button>
                    <!-- SearchComponent integration -->
                    <SearchComponent />

                    <!-- Profile Area -->
                    <div class="relative">
                        <button @click="toggleSection">
                            <UserDummyImage class='w-12 h-12 rounded-full border overflow-hidden' />
                        </button>
                        <div v-if="showSection" class="p-4 border rounded-xl absolute right-0 bg-white shadow-lg z-50 w-[300px]">
                            <div class="space-y-5 text-center">
                                <div class="relative">
                                    <button class="absolute right-2 top-0" @click="closetoggleSection"><Icon class="w-5"  icon="weui:close-outlined" /></button>
                                    <div class="text-center">
                                        <div class="flex flex-col items-center gap-2" @click="togglePopup">
                                            <div class="relative border-2 overflow-hidden rounded-full border-slate-700 group">
                                                <UserDummyImage class='w-12 h-12 rounded-full border overflow-hidden' />
                                                <button class="absolute right-1 bottom-1 bg-slate-200 shadow-lg w-5 h-5 flex justify-center items-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                    <Icon icon="bx:edit" />
                                                </button>
                                            </div>
                                            <span class="text-xs ">{{ $page.props.auth.user.email }}</span>
                                        </div>
                                    </div>
                                    <!-- Popup Modal -->
                                    <div v-if="showPopup"
                                        class="absolute z-50 w-[300px] top-[50px] right-0 md:right-0 bg-gray-600 bg-opacity-50 flex items-center justify-center shadow-xl">
                                        <form class="bg-white p-4 rounded shadow-lg" ref="popupModal">
                                            <div class="mb-4">
                                                <label for="fileUpload"
                                                    class="block text-sm font-medium text-gray-700 mb-3">Upload User
                                                    Image</label>
                                                <input type="file" id="fileUpload" class="mt-1 block w-full border">
                                            </div>
                                            <div class="flex justify-end">
                                                <button @click="saveImage"
                                                    class="bg-[#0B57D0] hover:bg-[#3169c5] text-white font-bold py-2 px-4 rounded">Upload</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <button @click="$inertia.post(route('logout'))"
                                    class="border border-slate-400 py-2 px-6 rounded-lg text-sm hover:bg-slate-600 hover:text-white transition-all duration-500">
                                    Log Out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Body -->
                <div class="p-5 pt-0">
                    <slot />
                </div>
            </div>
            <ComposeMessage class="absolute right-0 sm:right-8 bottom-0" v-show="isComposeModalShow"
                v-model="isComposeModalShow" />
        </div>
    </div>
</template>

<style>
/* Your global styles */
</style>
