<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Icon} from '@iconify/vue';
import {onMounted, ref} from 'vue';
import {Head, Link, router, usePage} from '@inertiajs/vue3';


const handleMoveToTrashButton = (emailId) => {
    if (route().current() === 'inbox') {
        router.delete(route('emails.destroy', emailId));
    } else if (route().current() === 'trash') {
        router.delete(route('emails.permanent.destroy', emailId));
    }
};

const handleMarkAsReadButton = (emailId) => {
    router.visit(route('toggle-as-read', emailId));
};

const handleRestoreButton = (emailId) => {
    router.visit(route('emails.restore', emailId));
};

const handleToggleImportantButton = (emailId) => {
    router.visit(route('toggle-as-important', emailId));
};

const handleToggleStarredButton = (emailId) => {
    router.visit(route('toggle-as-starred', emailId));
};

const props = defineProps({
    emails: Object,
    totalUnreadEmails: Number,
});

const totalUnreadEmailsCount = () => {
    if (props.totalUnreadEmails && props.totalUnreadEmails > 0) {
        return '(' + props.totalUnreadEmails + ')'
    }
    return '';
};

const selectedEmailList = ref([])

const handleAllSelectButton = () => {
    if (props.emails.data.length === selectedEmailList.value.length) {
        selectedEmailList.value = []
    } else {
        props.emails.data.map(function (email) {
            selectedEmailList.value.push(email.id)
        })
    }

    console.log('selected', selectedEmailList.value)
}

const handleSingleSelectButton = (emailId) => {
    selectedEmailList.value.push(emailId)
}

const handleSubmitMarkReadAll = () => {
    router.post(route('emails.mark-read-all'), {
        emails: selectedEmailList.value
    })
}

const handleDeleteMany = () => {
    router.post(route('emails.delete-many'), {
        emails: selectedEmailList.value
    })
}


Echo.private('App.Models.User.' + usePage().props.auth.user.id)
    .listen('ReceiveEmailBroadcastEvent', (event) => {
        console.log('hello there from email receives')
        router.visit(route(route().current()), {
            only: ['emails'],
        })
    });

</script>

<template>

    <Head :title="route().current()+totalUnreadEmailsCount()"/>
    <AuthenticatedLayout>
        <div
            class="h-[calc(100vh-105px)] bg-transparent shadow-xl sm:rounded-lg flex flex-col justify-between rounded-xl">
            <div class="flex-none flex items-center justify-between bg-white px-5 py-5 rounded-r-xl rounded-t-xl">
                <div class="flex items-center gap-2 md:gap-5">
                    <div class="relative group">
                        <input type="checkbox"
                               :checked="selectedEmailList.length === props.emails.data.length & selectedEmailList.length !== 0"
                               class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 focus:ring-2 transition duration-150 ease-in-out cursor-pointer"
                               @change="handleAllSelectButton"/>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Mark All
                        </span>
                    </div>
                    <!-- Refresh Button -->
                    <div class="relative group">
                        <Link :href="route(route().current())"
                              class="w-9 h-9 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full">
                            <Icon icon="tdesign:refresh"/>
                        </Link>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            Refresh
                        </span>
                    </div>
                    <template v-if="route().current() === 'inbox'">
                        <div class="relative group">
                            <button @click="handleDeleteMany"
                                    class="w-9 h-9 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full">
                                <Icon icon="tabler:trash"/>
                            </button>
                            <span
                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Delete Selected
                            </span>
                        </div>

                        <div class="relative group">
                            <button @click="handleSubmitMarkReadAll"
                                    class="w-9 h-9 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full">
                                <Icon icon="octicon:unread-24"/>
                            </button>
                            <span
                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                Read All
                            </span>
                        </div>
                    </template>
                </div>
                <div class="flex items-center gap-5">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-700 text-sm leading-tight">
                            {{ `${props.emails.from ?? 0} - ${props.emails.to ?? 0} of ${props.emails.total}` }}
                        </span>
                        <Link class="text-gray-700 text-sm disabled:opacity-10" :href="emails.prev_page_url"
                              :disabled="!!emails.prev_page_url">
                            <Icon icon="ri:arrow-left-s-line"/>
                        </Link>
                        <Link class="text-gray-700 text-sm disabled:opacity-10" :href="emails.next_page_url"
                              :disabled="!!emails.next_page_url">
                            <Icon icon="ri:arrow-right-s-line"/>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white flex items-center shadow-sm">
                <button
                    class="px-9 py-3 w-[300px] font-medium flex items-center text-md border-b border-[#0B57D0] text-[#0B57D0] hover:bg-slate-100">
                    <Icon icon="material-symbols:broken-image-outline" class="mr-3"/>
                    Primary {{ totalUnreadEmailsCount() }}
                </button>
            </div>

            <!-- Main Content Area -->
            <div class="flex-auto overflow-y-auto">
                <table class="w-full">
                    <tbody class="border-t">
                    <tr v-for="(email, index) in props.emails.data" :key="index"
                        :class="['px-5 py-2 flex-col md:flex-row md:items-center flex border-b w-full items-start space-y-1 md:space-x-2 hover:shadow-md group', email.is_read ? '' : 'bg-white']">
                        <!-- First Column: 3/12 width -->
                        <td class="flex items-center space-x-2 overflow-hidden overflow-ellipsis w-[250px]">
                            <input type="checkbox" :checked="selectedEmailList.find((item) => item === email.id)"
                                   @change="handleSingleSelectButton(email.id)"
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-0 transition duration-150 ease-in-out cursor-pointer"/>

                            <span @click="handleToggleStarredButton(email.id)" class="cursor-pointer">
                                    <Icon :icon="email.is_starred ? 'ph:star-fill' : 'ph:star-light'"
                                          :class="{ 'text-slate-700 w-5': !email.is_starred, 'text-yellow-500 w-5': email.is_starred }"/>
                                </span>

                            <span @click="handleToggleImportantButton(email.id)" class="cursor-pointer">
                                    <Icon v-if="email.is_important" icon="material-symbols:label-important-sharp"
                                          style="color: #F3C663"/>
                                    <template v-else>
                                        <Icon icon="material-symbols:label-important-outline" width="1.2em"
                                              height="1.2em"/>
                                    </template>
                                </span>

                            <button class="flex flex-wrap gap-1 pl-2 justify-start">
                                <template v-for="(receiver, index) in email.receivers" :key="index">
                                    <!-- Show only the first two receivers -->
                                    <span v-if="index < 2" class="font-bold text-xs text-left">
                                            {{
                                            receiver.receiver_id === $page.props.auth.user.id ? 'me' :
                                                receiver.user.name
                                        }},
                                        </span>
                                </template>
                                <!-- Show "more" if there are more than 2 receivers -->
                                <span v-if="email.receivers.length > 2" class="font-bold text-xs text-left">
                                        and {{ email.receivers.length - 2 }} more
                                    </span>
                            </button>
                        </td>
                        <!-- Second Column: 8/12 width -->
                        <td class="flex-grow space-x-2 md:min-w-[300px] md:pl-5 line-clamp-1">
                            <template v-if="route().current() !== 'trash'">
                                <Link :href="route('emails.show', email.id)"
                                      class="flex gap-1 md:flex-row flex-col">
                                    <strong class="whitespace-nowrap msg-subject-text">{{ email.subject }}</strong>
                                    <span class="hidden md:inline-block">-</span>
                                    <span class="block whitespace-normal msg-text">
                                        {{
                                            email.messages[0].content.substring(0, 115).replace(/<[^>]*>?/gm, '')
                                        }}
                                    </span>
                                </Link>
                            </template>
                            <template v-else>
                                <strong class="whitespace-nowrap">{{ email.subject }}</strong>-
                                <span class="block whitespace-normal msg-text">
                                        {{ email.messages[0].content.substring(0, 115).replace(/<[^>]*>?/gm, '') }}
                                    </span>
                            </template>
                        </td>
                        <!-- Third Column: Last in row -->
                        <td
                            class="flex-none md:ml-auto whitespace-nowrap flex items-center space-x-3 relative w-[72px] group">
                            <div class="flex items-center space-x-3 group-hover:hidden">
                                    <span v-if="email.has_attachment">
                                        <Icon icon="ph:paperclip-horizontal-light"/>
                                    </span>
                                <span>{{ email.created_at }}</span>
                            </div>
                            <div
                                class="items-center justify-center space-x-3 absolute right-0 top-1/2 -translate-y-1/2 w-[70px] hidden md:group-hover:flex">
                                <button @click="handleMoveToTrashButton(email.id)"
                                        class="w-6 h-6 bg-slate-300 rounded-full inline-flex justify-center items-center">
                                    <Icon icon="tabler:trash"/>
                                </button>
                                <template v-if="route().current() !== 'trash'">
                                    <button @click="handleMarkAsReadButton(email.id)"
                                            class="w-6 h-6 bg-slate-300 rounded-full inline-flex justify-center items-center readStatus">
                                        <Icon icon="octicon:unread-24"/>
                                    </button>
                                </template>
                                <template v-else>
                                    <button @click="handleRestoreButton(email.id)"
                                            class="w-6 h-6 bg-slate-300 rounded-full inline-flex justify-center items-center readStatus">
                                        <Icon icon="hugeicons:waste-restore"/>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="flex-none flex items-center justify-center bg-white px-5 py-5">
                <p class="text-sm text-slate-700"><a href="#">Terms</a> · <a href="#">Privacy Policies</a></p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple-animation 600ms linear;
    background: rgba(0, 0, 0, 0.3);
    pointer-events: none;
}

.msg-subject-text {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.msg-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
