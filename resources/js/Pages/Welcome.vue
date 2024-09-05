<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img
            id="background"
            class="absolute -left-20 top-0 max-w-[877px]"
            src="https://laravel.com/assets/img/welcome/background.svg"
        />
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <img :src="$page.props.gene" alt="">
                    </div>
                </header>

                <main class="mt-6">
                    <div class="text-center">
                        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                            Welcome to Personal Email Client
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl md:mt-5 md:text-lg">
                            Login to your account from HR system link if you are an employee.
                        </p>
<!--                        <div>
                            <div class="-mx-3 flex justify-center">
                                <Link
                                    v-if="$page.props.auth.user"
                                    :href="route('inbox')"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Inbox
                                </Link>

                                <template v-else>
                                    <Link
                                        :href="route('login')"
                                        class="rounded border -md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Log in
                                    </Link>

                                    <Link
                                        v-if="canRegister"
                                        :href="route('register')"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Register
                                    </Link>
                                </template>
                            </div>
                        </div>-->
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                </footer>
            </div>
        </div>
    </div>
</template>
