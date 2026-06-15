<script setup>
import { Head, Link } from '@inertiajs/vue3'
import logoAsset from '/resources/image/logo.png'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
})
</script>

<template>
    <Head title="Welcome to KPFM" />

    <div
        class="relative min-h-screen flex flex-col
        bg-slate-50 dark:bg-zinc-950
        text-slate-900 dark:text-zinc-100
        font-sans antialiased"
    >
        <!-- Background -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:28px_28px] opacity-[0.03]"></div>

            <div class="absolute top-[-200px] left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-emerald-500/10 blur-[120px] rounded-full"></div>

            <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-green-400/10 blur-[120px] rounded-full"></div>
        </div>

        <!-- HEADER -->
        <header
            class="sticky top-0 z-50
            bg-white/70 dark:bg-zinc-950/70
            backdrop-blur-xl
            border-b border-slate-200/60 dark:border-zinc-800"
        >
            <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">

                <div class="flex items-center gap-3">
                    <img
                        :src="logoAsset"
                        class="h-10 w-auto"
                        alt="KPFM Logo"
                    />

                    <div class="leading-tight">
                        <div class="font-semibold text-sm">
                            Kaindoa Pig Farm
                        </div>
                        <div class="text-xs text-emerald-600 font-medium">
                            Management System
                        </div>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-3">

                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-500 transition"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm px-3 py-2 text-slate-600 hover:text-slate-900 dark:text-zinc-400"
                        >
                            Login
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-500 transition"
                        >
                            Register
                        </Link>
                    </template>

                </nav>

            </div>
        </header>

        <!-- MAIN -->
        <main class="flex-1 flex items-center justify-center px-6 py-10 relative z-10">

            <div class="max-w-4xl w-full text-center">

                <!-- STATUS -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                    bg-emerald-500/10 border border-emerald-500/20
                    text-emerald-600 text-xs mb-6"
                >
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    System Live
                </div>

                <!-- LOGO -->
                <div class="flex justify-center mb-6">

                    <div class="relative">

                        <div class="absolute inset-0 bg-emerald-500/20 blur-3xl rounded-full"></div>

                        <div class="relative
                            bg-white/70 dark:bg-zinc-900/60
                            backdrop-blur-xl
                            border border-white/20 dark:border-zinc-800
                            rounded-2xl
                            p-5 shadow-xl"
                        >
                            <img
                                src="https://res.cloudinary.com/do3i7dfhc/image/upload/v1781540896/logonew_nqg9zo.png"
                                class="h-40 sm:h-48 w-auto object-contain"
                                alt="KPFM Logo"
                            />
                        </div>

                    </div>

                </div>

                <!-- TITLE -->
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                    Kaindoa Pig Farm
                    <span class="block text-emerald-600">
                        Management System
                    </span>
                </h1>

                <!-- DESCRIPTION -->
                <p class="mt-4 text-sm sm:text-base text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto">
                    A smart livestock management system for breeding,
                    health tracking, production records and farm analytics.
                </p>

                <!-- BUTTONS -->
                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">

                    <template v-if="!$page.props.auth.user">

                        <Link
                            :href="route('login')"
                            class="px-6 py-3 rounded-xl
                            bg-emerald-600 text-white font-semibold
                            hover:bg-emerald-500 transition"
                        >
                            Login
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-6 py-3 rounded-xl
                            border border-slate-300 dark:border-zinc-700
                            hover:bg-white dark:hover:bg-zinc-900 transition"
                        >
                            Register
                        </Link>

                    </template>

                    <template v-else>
                        <Link
                            :href="route('dashboard')"
                            class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500"
                        >
                            Go to Dashboard →
                        </Link>
                    </template>

                </div>

                <!-- QUICK STATS -->
                <div class="mt-10 grid grid-cols-3 gap-6 max-w-md mx-auto">

                    <div>
                        <div class="text-xl font-bold text-emerald-600">24/7</div>
                        <div class="text-xs text-slate-500">Monitoring</div>
                    </div>

                    <div>
                        <div class="text-xl font-bold text-emerald-600">Real-Time</div>
                        <div class="text-xs text-slate-500">Tracking</div>
                    </div>

                    <div>
                        <div class="text-xl font-bold text-emerald-600">Smart</div>
                        <div class="text-xs text-slate-500">Reports</div>
                    </div>

                </div>

                <!-- PREVIEW BOX -->
                <div class="mt-10 max-w-3xl mx-auto">

                    <div class="rounded-2xl p-6
                        bg-white/80 dark:bg-zinc-900/80
                        backdrop-blur-xl
                        border border-slate-200 dark:border-zinc-800
                        shadow-lg"
                    >

                        <div class="grid sm:grid-cols-3 gap-4 text-left">

                            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                                🐖 <div class="text-sm font-semibold mt-1">Breeding</div>
                                <div class="text-xs text-slate-500">Manage livestock lineage</div>
                            </div>

                            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                                🩺 <div class="text-sm font-semibold mt-1">Health</div>
                                <div class="text-xs text-slate-500">Track treatments</div>
                            </div>

                            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                                📊 <div class="text-sm font-semibold mt-1">Analytics</div>
                                <div class="text-xs text-slate-500">Farm insights</div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </main>

        <!-- FOOTER -->
        <footer class="border-t border-slate-200 dark:border-zinc-800 bg-white/70 dark:bg-zinc-950/70 backdrop-blur-xl">

            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between text-xs text-slate-500">

                <div>
                    © {{ new Date().getFullYear() }} KPFM
                </div>

                <div class="font-mono">
                    Laravel v{{ laravelVersion }} | PHP v{{ phpVersion }}
                </div>

            </div>

        </footer>

    </div>
</template>