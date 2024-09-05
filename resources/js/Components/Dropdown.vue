<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';

const isOpen = ref(false);
const selected = ref('Select'); // Default text when no option is selected

const options = ref([
    'Option 1',
    'Option 2',
    'Option 3',
    // Add more options as needed
]);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (option) => {
    selected.value = option;
    isOpen.value = false; // Close dropdown after selection
};
</script>
<template>
    <div class="relative">
        <!-- Dropdown Button -->
        <button @click="toggleDropdown"
            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center gap-2 focus:outline-none">
            <span>{{ selected }}</span>
            <Icon icon="ic:round-keyboard-arrow-down" />
        </button>

        <!-- Dropdown Content -->
        <div v-if="isOpen" class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-md overflow-hidden z-10">
            <ul>
                <li v-for="(option, index) in options" :key="index" @click="selectOption(option)">
                    <button class="block text-gray-800 px-4 py-2 hover:bg-gray-100 focus:outline-none w-full text-left">
                        {{ option }}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
/* No additional CSS needed if using Tailwind CSS utility classes */
</style>