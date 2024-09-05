<script setup>
import {ref, watch} from 'vue';
import {Icon} from '@iconify/vue';
import {Link} from "@inertiajs/vue3";

const q = ref('');
const emails = ref([]);
const handleSearchAPI = (value) => {
    axios.get(route('emails.search'), {
        params: {
            q: value
        }
    }).then((response) => {
        emails.value = response.data
    })
}

watch(q, (value, oldValue, onCleanup) => {
    handleSearchAPI(value)
})
</script>

<template>
    <div class="max-w-1/2 relative">
        <div class="flex relative">
            <icon icon="iconamoon:search-thin"
                  class="absolute top-1/2 -translate-y-1/2 left-3 w-7 h-7 hover:bg-gray-200 p-[5px] rounded-full cursor-pointer"/>
            <input
                type="text"
                v-model="q"
                class="bg-[#EAF1FB] border border-gray-300 text-gray-900 text-base rounded-full focus:ring-0 focus:outline-none focus:border-gray-300 block p-2.5 xl:w-[700px] px-12 w-full"
                placeholder="Search here"
            />
        </div>
        <ul v-if="q && emails.length > 0"
            class="bg-white absolute top-12 border rounded-lg max-h-80 overflow-auto z-40 w-full transition-opacity duration-300">
            <li v-for="result in emails" :key="result.id">
                <Link :href="route('emails.show', result.id)">
                    <div class="px-4 py-2 border-t border-slate-200 flex justify-between items-center cursor-pointer">
                        <div>
                            <p class="text-base text-slate-800 font-bold">
                                {{
                                    result.subject + ": " + result.messages[0].content.substring(0, 30).replace(/<[^>]*>?/gm, '')
                                }}
                            </p>
                            <p class="text-sm flex gap-2 text-slate-600">
                                <span>{{
                                        result.from.email === $page.props.auth.user.email ? 'me' : result.from.email
                                    }}, To: </span>
                                <template v-for="receiver in result.receivers" :key="receiver.id">
                                <span v-if="$page.props.auth.user.email !== receiver.user.email">
                                    {{ receiver.user.email }}
                                </span>
                                </template>
                            </p>
                        </div>
                        <span class="text-sm flex gap-2 text-slate-600">{{ result.created_at }}</span>
                    </div>
                </Link>
            </li>
        </ul>
    </div>
</template>
