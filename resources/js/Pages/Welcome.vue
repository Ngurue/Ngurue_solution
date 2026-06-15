<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />
    
    <div class="relative min-h-screen flex flex-col justify-between bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-950 dark:to-zinc-900 text-zinc-800 dark:text-zinc-100 font-sans antialiased selection:bg-emerald-500 selection:text-white">
        
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="size-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-black text-lg shadow-md shadow-emerald-600/20">
                    S
                </div>
                <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-teal-300">
                    Shamba Manager
                </span>
            </div>

            <nav v-if="canLogin" class="flex items-center space-x-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-sm font-medium px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20 transition-all duration-200"
                >
                    Dashboard
                </Link>
            </nav>
        </header>

        <main class="flex-1 flex flex-col items-center justify-center text-center px-6 max-w-4xl mx-auto">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-medium mb-6 animate-pulse">
                <span>Smart Farming Management Platform</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight mb-6 max-w-2xl leading-[1.15]">
                Simamia Mifugo na Mazao Yako kwa 
                <span class="bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-teal-300">
                    Ufanisi Zaidi
                </span>
            </h1>

            <p class="text-base sm:text-lg text-zinc-500 dark:text-zinc-400 max-w-xl mb-10 leading-relaxed">
                Mfumo rahisi na wa kisasa wa kidijitali ulioundwa maalum kusaidia wafugaji kufuatilia breeds, afya ya mifugo, na uzalishaji wa kila siku wa shamba lao.
            </p>

            <div v-if="!$page.props.auth.user && canLogin" class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                <Link
                    :href="route('login')"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/25 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-950 transition-all duration-200 text-base"
                >
                    Ingia (Log in)
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-white text-zinc-800 font-semibold border border-zinc-200 hover:bg-zinc-50 dark:bg-zinc-900 dark:text-zinc-100 dark:border-zinc-800 dark:hover:bg-zinc-800/50 focus:outline-none focus:ring-2 focus:ring-zinc-500 transition-all duration-200 text-base"
                >
                    Jisajili (Register)
                </Link>
            </div>

            <div v-else-if="$page.props.auth.user" class="mt-2">
                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/25 hover:bg-emerald-500 transition-all duration-200 text-base"
                >
                    Ingia Kwenye Mfumo &rarr;
                </Link>
            </div>
        </main>

        <footer class="w-full max-w-7xl mx-auto px-6 py-8 flex flex-col sm:flex-row justify-between items-center border-t border-zinc-200/50 dark:border-zinc-800/50 text-xs text-zinc-400 dark:text-zinc-500 gap-4">
            <div>
                &copy; 2026 Shamba Manager. Haki zote zimehifadhiwa.
            </div>
            <div class="flex items-center space-x-1 font-mono">
                <span>Laravel v{{ laravelVersion }}</span>
                <span>•</span>
                <span>PHP v{{ phpVersion }}</span>
            </div>
        </footer>
    </div>
</template>