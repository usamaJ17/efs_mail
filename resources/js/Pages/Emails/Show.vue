<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Icon} from '@iconify/vue';
import {ref} from 'vue';
import {Head, Link, useForm, usePage} from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import BlotFormatter from 'quill-blot-formatter/dist/BlotFormatter'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import UserDummyImage from "@/Components/UserDummyImage.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    email: Object,
});

const isReplyShow = ref(false);
const isForwardShow = ref(false);
const toggleReplyOption = () => {
    form.reset()
    isReplyShow.value = !isReplyShow.value;
    if (isReplyShow.value) {
        isForwardShow.value = false
    }
}

// reply composer conf. start
const page = usePage();
const quillContent = ref('');
const forwardQuillContent = ref('');
const recipientInput = ref(null);

const form = useForm({
    receivers: [],
    subject: '',
    sender_id: page.props.auth.user.id,
    reply_to: null,
    content: '',
    message: '',
    files: [],
});

const editorConfiguration = {
    module: BlotFormatter,
}

const toolbar = [
  [{ header: [1, 2, 3, 4, 5, 6, false] }],
  ['bold', 'italic', 'underline', 'strike', 'blockquote'],
  [{ align: [] }],
  [{ list: 'ordered' }, { list: 'bullet' }],
  ['link',]
]

const fileInput = ref(null);
const replyFileInput = ref(null);

const handleFormSubmit = () => {
    form.content = quillContent.value.getHTML();
    console.log(form.content)
    form.post(route('emails.store-reply', props.email.id));
    form.reset();
};

const handleFileUpload = () => {
    let file = fileInput.value.files[0];
    if (isImage(file)) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            form.files.push({file, preview: reader.result, is_image: true});
        };
    } else {
        form.files.push({file, preview: null, is_image: false});
    }
};

const handleReplyFileUpload = () => {
    let file = replyFileInput.value.files[0];
    if (isImage(file)) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            form.files.push({file, preview: reader.result, is_image: true});
        };
    } else {
        form.files.push({file, preview: null, is_image: false});
    }
};

const removeFile = (index) => {
    form.files.splice(index, 1)
};

const isImage = (file) => {
    if (!file) return false;
    const acceptedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    return acceptedImageTypes.includes(file.type);
};

const handleSpecificMessageReplyToButton = (messageId) => {
    form.reply_to = messageId
    isReplyShow.value = true
}

const selectedMessageId = ref(null); // Ref to keep track of the selected message

const handleMailInfo = (messageId) => {
    selectedMessageId.value = selectedMessageId.value === messageId ? null : messageId;
};

// forward
const toggleForwardOption = () => {
    isForwardShow.value = !isForwardShow.value;
    if (isForwardShow.value) {
        isReplyShow.value = false

        form.subject = 'Fwd: ' + props.email.subject;

        let lastMessage = props.email.messages[props.email.messages.length - 1];
        let forwardContent =
            "---------- Forwarded message ---------<br>" +
            "From: " + lastMessage.sender.name + " < " + lastMessage.sender.email + " ><br>" +
            "Date: " + lastMessage.created_at + " <br>" +
            "Subject: " + props.email.subject + " <br>" +
            "To: " + (lastMessage.reply_message == null
                ? props.email.receivers.map(receiver => receiver.user.name + " < " + receiver.user.email + " >").join(", ")
                : lastMessage.reply_message.sender.name + " <" + lastMessage.reply_message.sender.email + ">") +
            "<br><br>" + props.email.messages.map(message => message.content).join("<br>");


        console.log(forwardContent)
        // add previous message content
        forwardQuillContent.value.setHTML(forwardContent);
    }
}

const addRecipient = () => {
    form.receivers.push(recipientInput.value);
    recipientInput.value = null;
};

const removeRecipient = (index) => {
    form.receivers.splice(index, 1);
};

const handleForwardButtonSubmit = () => {
    form.message = forwardQuillContent.value.getHTML();
    form.post(route('forward'));
}

// State to manage visibility of messages
const visibleMessages = ref({});

// Method to toggle visibility of a message content
const toggleVisibility = (index) => {
  visibleMessages.value[index] = !visibleMessages.value[index];
}
</script>

<template>

    <Head title="Details"/>
    <AuthenticatedLayout>
        <div class="h-[calc(100vh-105px)] bg-transparent relative shadow-md bg-white sm:rounded-lg rounded-xl overflow-y-auto">
            <div class="px-5 py-4 flex justify-between items-center absolute md:relative">
                <div class=" flex space-x-4">
                    <!-- back Button -->
                    <div class="relative group inline-block">
                        <Link :href="route('inbox')"
                              class="w-9 h-9 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full">
                            <Icon icon="bi:arrow-left"/>
                        </Link>
                    </div>
                </div>
            </div>
            <!-- mail area -->
            <div class="px-5 flex items-start md:items-center space-x-2 mt-5 md:mt-0 pl-20 md:pl-5">
                <h2 class="text-base md:text-2xl font-bold text-[#1f1f1f]">{{ props.email.subject }}</h2>
                <span class="bg-[#dddddd] px-2 py-[1px] inline-block text-xs rounded-md text-slate-800">inbox</span>
            </div>

            <div v-for="(message, index) in email.messages" :key="email.id" class="px-5 py-4 mb-4 border-b last:border-none ">
                <div @click="toggleVisibility(index)" class="flex md:items-center justify-between flex-col sm:flex-row cursor-pointer">
                    <!-- Mailbody Header -->
                    <div class="flex items-start space-x-4">
                        <UserDummyImage class="w-12 h-12 rounded-full border border-black shrink-0" />
                        <div class="max-w-[70%]">
                            <h4 class="text-base text-slate-900 font-bold">{{ message.sender.name }}</h4>

                            <div v-if="visibleMessages[index]" class="text-sm text-slate-600 flex items-center space-x-1">
                                <span>to</span>
                                <span>{{ message.reply_message == null ? 'all' : message.reply_message.sender.name }}</span>
                                <div class="relative">
                                    <button class="text-2xl p-0" @click.stop="handleMailInfo(message.id)">
                                    <Icon icon="ri:arrow-drop-down-fill" />
                                    </button>
                                    <div  v-if="selectedMessageId === message.id" class="absolute -left-[100px] md:left-0 top-6 bg-slate-50 shadow-lg p-2 border z-40 min-w-80">
                                    <ul>
                                        <li class="flex items-start mb-[2px] last:mb-0"><span class="w-20 text-end pr-2">from:</span> {{message.sender.name}}</li>
                                        <li class="flex items-start mb-[2px] last:mb-0">
                                            <span class="w-20 text-end pr-2 ">to:</span>
                                            <div class="flex flex-col">
                                                <template v-for="receiver in email.receivers" :key="receiver.id">
                                                    <span v-if="receiver.user.email !== $page.props.auth.user.email" class="flex w-full flex-col">
                                                        <span>{{ receiver.user.name }}</span>
                                                        <span class="text-[12px] pb-1 -mt-1"> <{{ receiver.user.email }}></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </li>
                                        <li class="flex items-start mb-[2px] last:mb-0"><span class="w-20 text-end pr-2">date:</span> {{message.created_at}}</li>
                                        <li class="flex items-start mb-[2px] last:mb-0"><span class="w-20 text-end pr-2">subject:</span> {{email.subject}}</li>
                                        <li class="flex items-start mb-[2px] last:mb-0"><span class="w-20 text-end pr-2">mailed-by:</span> {{email.from.email.split('@')[1]}}</li>
                                    </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <span v-if="!visibleMessages[index]" class="minimized-text text-slate-600 text-sm" v-html="message.content"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 shrink-0">
                    <p class="text-xs text-slate-600">{{ message.created_at }}</p>
                    <div class="relative group inline-block">
                        <button class="w-9 h-9 text-gray-600 flex items-center justify-center rounded-full">
                        <Icon icon="tabler:star" />
                        </button>
                    </div>
                    <!-- Reply Button -->
                    <div class="relative group inline-block">
                        <button @click.stop="handleSpecificMessageReplyToButton(message.id)" class="w-9 h-9 bg-gray-100 text-gray-600 flex items-center justify-center rounded-full">
                        <Icon icon="material-symbols:reply" />
                        </button>
                        <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-max px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Reply
                        </span>
                    </div>
                    </div>
                </div>
                <!-- Mail body -->
                <div v-if="visibleMessages[index]" class="mt-5 md:pl-16 rk-mailBody">
                    <span v-html="message.content"></span>
                    <div v-if="message.attachments && message.attachments.length > 0" class="mt-7 flex flex-wrap md:grid-cols-3 gap-4">
                        <div v-for="attachment in message.attachments" :key="attachment.id" class="flex items-center justify-center flex-col w-[100px] h-[100px] text-center border p-1 rounded-md relative group">
                            <a :href="attachment.original_url" target="__blank" class="absolute top-0 right-0 bg-gray-700 text-white rounded-full p-1 hover:bg-gray-800 focus:outline-none transition duration-300 ease-in-out">
                                <Icon icon="tabler:download" class="w-4 h-4" />
                            </a>
                            <Icon :icon="'mdi:file'" class="w-9 h-9" />
                            <span class="text-xs max-h-3 overflow-hidden">{{ attachment.file_name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- reply and forward button -->
            <div class="px-5 py-4 mt-4 flex space-x-4">
                <button @click="toggleReplyOption"
                        class="border border-[#747775] text-[#444746] font-semibold px-8 py-2 rounded-full bg-white hover:bg-slate-200 transition-all duration-500 text-sm flex items-center">
                    <Icon icon="material-symbols:reply" class="mr-1 text-base"/>
                    Reply
                </button>
                <button @click="toggleForwardOption"
                        class="border border-[#747775] text-[#444746] font-semibold px-8 py-2 rounded-full bg-white hover:bg-slate-200 transition-all duration-500 text-sm flex items-center">
                    <Icon icon="solar:forward-outline" class="mr-1 text-base"/>
                    Forward
                </button>
            </div>
            <!-- Reply Form Area -->
            <div class="min-h-[100px] px-5 py-4" v-if="isReplyShow && isForwardShow===false">
                <form @submit.prevent="handleFormSubmit" class="p-3 flex flex-col justify-between  gap-0">
                    <div class="flex-auto h-full  rounded-2xl shadow-xl">
                        <!-- <QuillEditor ref="quillContent"  :options="editorConfiguration.value"/> -->
                        <quill-editor ref="quillContent" :modules="editorConfiguration" :toolbar="toolbar" />
                        <span class="text-red-400 text-sm mt-1 block flex-none" v-if="form.errors.content"> {{
                                form.errors.content
                            }}
                        </span>
                    </div>
                    <div class="py-2 flex-none relative">
                        <div class="flex gap-2 my-2">
                            <div v-for="(file, index) in form.files" :key="index">
                                <template v-if="file.is_image"
                                          class="w-[100px] h-[100px] rounded-xl overflow-hidden border relative group">
                                    <img :src="file.preview" alt="File Preview"
                                         class="w-[100px] h-[100px] border rounded">
                                    <button @click.prevent="removeFile"
                                            class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                                        <Icon icon="ic:round-close" class="w-4 h-4"/>
                                    </button>
                                </template>
                                <template v-else class="w-[100px] h-[100px] rounded-xl overflow-hidden border ">
                                    <div
                                        class="flex items-center justify-center flex-col w-[100px] h-[100px] text-center border p-1 rounded-md relative group">
                                        <button @click.prevent="removeFile(index)"
                                                class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                                            <Icon icon="ic:round-close" class="w-4 h-4"/>
                                        </button>
                                        <Icon icon="mdi:file" class="w-9 h-9"/>
                                        <span class="text-xs max-h-3 overflow-hidden">{{ file.file.name }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div>
                            <label for="reply-file-upload"
                                   class="inline-block px-3 py-3 cursor-pointer bg-black text-white rounded-full text-sm text-center mr-2.5 transition duration-500 ease-in-out hover:bg-sky-800 w-[120px]">Upload
                                File</label>
                            <input ref="replyFileInput" type="file" @change="handleReplyFileUpload" id="reply-file-upload"
                                   class="hidden">
                        </div>
                        <button
                            class="px-9 py-2 w-[130px] bg-sky-800 text-white rounded-3xl inline-flex items-center gap-1"
                            type="submit">Send
                            <Icon icon="carbon:send-filled"/>
                        </button>
                    </div>
                </form>
            </div>
            <!-- Forward Form Area -->
            <div class="min-h-[100px] px-5 py-4" v-show="isForwardShow& isReplyShow===false">
                <div class="p-3 flex flex-col justify-between gap-0 shadow-xl rounded-2xl">
                    <div class="flex-none">
                        <div class="flex flex-wrap items-center mb-3 gap-2 border-b">
                            <span>To:</span>
                            <template v-for="(receiver, index) in form.receivers" :key="index">
                                <div class="flex items-center bg-gray-200 p-1 rounded-full">
                                    <span class="mx-2 text-xs">{{ receiver }}</span>
                                    <button @click.prevent="removeRecipient(index)" class="text-red-500">
                                        <Icon icon="ic:round-close" class="w-4 h-4"/>
                                    </button>
                                </div>
                            </template>
                            <input v-model="recipientInput" @keyup.enter="addRecipient" type="text"
                                   placeholder="Add recipient"
                                   class="flex-grow border-none focus:ring-0 focus:shadow-none focus:border-none outline-none"/>
                            <InputError :message="form.errors.receivers"/>
                            <template v-for="(receiver, index) in form.receivers" :key="index">
                        <span class="text-red-400 text-sm mt-1 block flex-none"
                              v-if="form.errors[`receivers.${index}`]">
                            {{ form.receivers[index] + 'is not a valid employee email' }}
                        </span>
                            </template>
                        </div>
                        <div class="flex border-b items-center mb-3">
                            <span>Subject:</span>
                            <input v-model="form.subject" type="text"
                                   class="w-full block border-none focus:ring-0 focus:shadow-none focus:border-none">
                            <span class="text-red-400 text-sm mt-1 block flex-none" v-if="form.errors.subject"> {{
                                    form.errors.subject
                                }}</span>
                        </div>
                    </div>
                    <div class="flex-auto h-full ">
                        <quill-editor ref="forwardQuillContent" :modules="editorConfiguration" :toolbar="toolbar" />
                        <span class="text-red-400 text-sm mt-1 block flex-none" v-if="form.errors.content"> {{
                                form.errors.content
                            }}
                        </span>
                    </div>
                    <div class="py-2 flex-none relative">
                        <div class="flex gap-2 my-2">
                            <div v-for="(file, index) in form.files" :key="index">
                                <template v-if="file.is_image"
                                          class="w-[100px] h-[100px] rounded-xl overflow-hidden border relative group">
                                    <img :src="file.preview" alt="File Preview"
                                         class="w-[100px] h-[100px] border rounded">
                                    <button @click.prevent="removeFile"
                                            class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                                        <Icon icon="ic:round-close" class="w-4 h-4"/>
                                    </button>
                                </template>
                                <template v-else class="w-[100px] h-[100px] rounded-xl overflow-hidden border ">
                                    <div
                                        class="flex items-center justify-center flex-col w-[100px] h-[100px] text-center border p-1 rounded-md relative group">
                                        <button @click.prevent="removeFile(index)"
                                                class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                                            <Icon icon="ic:round-close" class="w-4 h-4"/>
                                        </button>
                                        <Icon icon="mdi:file" class="w-9 h-9"/>
                                        <span class="text-xs max-h-3 overflow-hidden">{{ file.file.name }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div>
                            <label for="file-upload"
                                   class="inline-block px-3 py-3 cursor-pointer bg-black text-white rounded-full text-sm text-center mr-2.5 transition duration-500 ease-in-out hover:bg-sky-800 w-[120px]">Upload
                                File</label>
                            <input ref="fileInput" type="file" @change="handleFileUpload" id="file-upload"
                                   class="hidden">
                        </div>
                        <button @click="handleForwardButtonSubmit"
                            class="px-9 py-2 w-[130px] bg-sky-800 text-white rounded-3xl inline-flex items-center gap-1"
                            type="submit">
                            Send
                            <Icon icon="carbon:send-filled"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.ql-editor {
    overflow-y: auto;
    height: 150px;
}

.ql-snow .ql-editor img {
    max-width: 100px;
    border: 1px solid #f1eded;
    box-shadow: 0 0 5px #eeeeee;
    border-radius: 10px;
}

.minimized-text {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Basic Styling */
/* Headings */
.rk-mailBody h1, .rk-mailBody h2, .rk-mailBody h3, .rk-mailBody h4, .rk-mailBody h5, .rk-mailBody h6 {
    font-family: inherit;
    font-weight: 500;
    line-height: 1.2;
}

.rk-mailBody h1 { font-size: 2.5rem; }
.rk-mailBody h2 { font-size: 2rem; }
.rk-mailBody h3 { font-size: 1.75rem; }
.rk-mailBody h4 { font-size: 1.5rem; }
.rk-mailBody h5 { font-size: 1.25rem; }
.rk-mailBody h6 { font-size: 1rem; }

/* Paragraphs */
.rk-mailBody p {
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #212529;
}

/* Blockquotes */
.rk-mailBody blockquote {
    margin: 0 0 1rem;
    padding: 0.5rem 1rem;
    border-left: 0.25rem solid #eceeef;
    color: #6c757d;
    font-size: 1.25rem;
}

/* Lists */
.rk-mailBody ul, .rk-mailBody ol {
    margin-top: 0;
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.rk-mailBody ul {
    list-style-type: disc;
}

.rk-mailBody ol {
    list-style-type: decimal;
}

.rk-mailBody dl {
    margin-top: 0;
    margin-bottom: 1rem;
}

.rk-mailBody dt {
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.rk-mailBody dd {
    margin-bottom: 0.5rem;
    margin-left: 0;
}

/* Links */
.rk-mailBody a {
    color: #007bff;
    text-decoration: none;
    background-color: transparent;
}

.rk-mailBody a:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* Images */
.rk-mailBody img {
    vertical-align: middle;
    border-style: none;
    max-width: 100%;
    height: auto;
}

/* Tables */
.rk-mailBody table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
}

.rk-mailBody th, .rk-mailBody td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.rk-mailBody thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.rk-mailBody tbody + tbody {
    border-top: 2px solid #dee2e6;
}

</style>
