<script setup>
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { QuillEditor } from '@vueup/vue-quill';
import BlotFormatter from 'quill-blot-formatter/dist/BlotFormatter'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {useForm} from "@inertiajs/vue3";


const form = useForm({
    receivers: [],
    subject: '',
    message: '',
    files: [],
});

const isComposeModalShow = defineModel();

const closeComposeModal = () => {
    isComposeModalShow.value = false;
};

// const editorConfiguration = ref({
//     debug: 'info',
//     modules: {
//         toolbar: ['bold', 'italic', 'underline', 'image', 'link', 'strike', { 'list': 'ordered' }, { 'list': 'bullet' }, 'blockquote', { 'header': [1, 2, 3, 4, 5, 6, false] }]
//     },
//     placeholder: 'Your Message here',
//     theme: 'snow'
// });

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
const recipientInput = ref(null);
const quill = ref(null);

const handleFormSubmit = () => {
    form.message = quill.value.getHTML();
    form.post(route('compose'),{
        onSuccess: function () {
            form.reset()
            isComposeModalShow.value = false
        }
    });
};

const handleFileUpload = () => {
    let file = fileInput.value.files[0];
    if (isImage(file)) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            form.files.push({ file, preview: reader.result, is_image: true });
        };
    } else {
        form.files.push({ file, preview: null, is_image: false });
    }

    console.log(form.files)
};

const removeFile = (index) => {
    form.files.splice(index, 1)
    console.log({ form })
};

const isImage = (file) => {
    if (!file) return false;
    const acceptedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    return acceptedImageTypes.includes(file.type);
};

const addRecipient = () => {
    form.receivers.push(recipientInput.value);
    recipientInput.value = null;
};

const removeRecipient = (index) => {
    form.receivers.splice(index, 1);
};
</script>

<template>
    <div class=" w-full sm:w-[550px] min-h-full md:min-h-[400px] bg-white shadow-lg shadow-slate-500 rounded-xl rounded-b-none z-50">
        <div class="bg-slate-200 px-2 py-1 flex items-start justify-between">
            <p class="text-sm">New Message</p>
            <button @click="closeComposeModal">
                <Icon icon="mingcute:close-line" />
            </button>
        </div>
        <div class="p-3 flex flex-col justify-between min-h-[450px] gap-0">
            <div class="flex-none">
                <div class="flex flex-wrap items-center mb-3 gap-2 border-b">
                    <span>To:</span>
                    <template v-for="(receiver, index) in form.receivers" :key="index">
                        <div class="flex items-center bg-gray-200 p-1 rounded-full">
                            <span class="mx-2 text-xs">{{ receiver }}</span>
                            <button @click.prevent="removeRecipient(index)" class="text-red-500">
                                <Icon icon="ic:round-close" class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                    <input v-model="recipientInput" @keyup.enter="addRecipient" type="text" placeholder="Add recipient"
                        class="flex-grow border-none focus:ring-0 focus:shadow-none focus:border-none outline-none" />
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
                form.errors.subject }}</span>
                </div>
            </div>
            <div class="flex-auto h-full">
                <!-- <QuillEditor :options="editorConfiguration.value" ref="quillEditor"/> -->
                <quill-editor ref="quill" :modules="editorConfiguration" :toolbar="toolbar" />
                <span class="text-red-400 text-sm mt-1 block flex-none"
                      v-if="form.errors.message"> {{ form.errors.message }}</span>
            </div>
            <div class="py-2 flex items-center flex-wrap relative">
                <div v-for="(file, index) in form.files" :key="index" class="inline-flex mr-2 my-2 relative">
                    <template v-if="file.is_image"
                        class="w-[100px] h-[100px] rounded-xl overflow-hidden border relative group">
                        <img :src="file.preview" alt="File Preview" class="w-[100px] h-[100px] border rounded">
                        <button @click.prevent="removeFile"
                            class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                            <Icon icon="ic:round-close" class="w-4 h-4" />
                        </button>
                    </template>
                    <template v-else class="w-[100px] h-[100px] rounded-xl overflow-hidden border ">
                        <div
                            class="flex items-center justify-center flex-col w-[100px] h-[100px] text-center border p-1 rounded-md relative group">
                            <button @click.prevent="removeFile(index)"
                                class="absolute top-0 right-0  bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none transition duration-300 ease-in-out">
                                <Icon icon="ic:round-close" class="w-4 h-4" />
                            </button>
                            <Icon icon="mdi:file" class="w-9 h-9" />
                            <span class="text-xs max-h-3 overflow-hidden">{{ file.file.name }}</span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="flex gap-2">
                <div>
                    <label for="file-upload" class="inline-block px-3 py-3 cursor-pointer bg-[#0B57D0] text-white rounded-full text-sm text-center mr-2.5 transition duration-500 ease-in-out hover:bg-[#2567d1] w-[120px]">Upload File</label>
                    <input ref="fileInput" type="file" @change="handleFileUpload" id="file-upload" class="hidden">
                </div>
                <button
                    @click="handleFormSubmit"
                    class="px-9 py-2 w-[130px] bg-[#0B57D0] text-white rounded-3xl inline-flex items-center gap-1 hover:bg-[#2567d1]"
                    type="submit">Send
                    <Icon icon="carbon:send-filled" />
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.ql-editor {
    overflow-y: auto;
    height: 250px !important;
}
.ql-snow .ql-editor img {
    max-width: 100px;
    border: 1px solid #f1eded;
    box-shadow: 0 0 5px #eeeeee;
    border-radius: 10px;
}
.ql-toolbar.ql-snow,
.ql-container.ql-snow {
    border: none;
    border-bottom: 1px solid #eeeeee;
}

</style>
