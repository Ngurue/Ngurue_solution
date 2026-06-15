<script setup>
import { Head, Link } from '@inertiajs/vue3';
// Import your fallback logo asset for compiling verification
import logoAsset from '/resources/image/logo.png'; 

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
    <Head title="Welcome to KPFM" />
    
    <div class="relative min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-zinc-950 text-slate-900 dark:text-zinc-100 font-sans antialiased selection:bg-emerald-500 selection:text-white">
        
        <!-- Premium UI Radial Grid Line Overlay -->
        <div class="absolute inset-0 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px] opacity-[0.03] dark:opacity-[0.05] pointer-events-none"></div>

        <!-- Glassmorphism Header Bar -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200/60 bg-white/80 backdrop-blur-md dark:border-zinc-800/60 dark:bg-zinc-950/80">
            <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
                
                <!-- Navbar Left Brand Flag -->
                <div class="flex items-center space-x-3">
                    <img 
                        src="https://res.cloudinary.com/do3i7dfhc/image/upload/v1781540896/logonew_nqg9zo.png" 
                        alt="KPFM Logo" 
                        class="h-8 w-auto object-contain"
                        @error="(e) => e.target.src = logoAsset" 
                    />
                    <div class="h-4 w-px bg-slate-200 dark:bg-zinc-800"></div>
                    <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-zinc-50 font-mono uppercase">
                        KPFM <span class="text-emerald-600 dark:text-emerald-400 font-normal lowercase font-sans">platform</span>
                    </span>
                </div>

                <!-- Navigation Controls -->
                <nav v-if="canLogin" class="flex items-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="text-xs font-semibold uppercase tracking-wider px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 transition"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-zinc-200 transition px-3 py-2"
                        >
                            Ingia
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="text-sm font-medium px-4 py-2 rounded-xl bg-emerald-600 text-white shadow-sm hover:bg-emerald-500 transition"
                        >
                            Jisajili
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Workspace Presentation Area -->
        <main class="flex-1 flex flex-col justify-center items-center py-16 px-6 relative z-10 w-full max-w-7xl mx-auto">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
                
                <!-- Left Hand Text & Call To Actions Block -->
                <div class="lg:col-span-5 flex flex-col items-center lg:items-start text-center lg:text-left">
                    
                    <!-- Large Centralized Logo Display Component Wrapper -->
                    <div class="mb-6 p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 shadow-md transition hover:scale-[1.01] duration-300 max-w-[240px]">
                        <img 
                            src="https://res.cloudinary.com/do3i7dfhc/image/upload/v1781540896/logonew_nqg9zo.png" 
                            alt="KPFM Main Corporate Logo" 
                            class="h-24 sm:h-28 w-auto object-contain drop-shadow-sm" 
                            @error="(e) => e.target.src = logoAsset"
                        />
                    </div>

                    <!-- System Status Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-6">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        System Online • v1.0
                    </div>

                    <!-- Main Dynamic Header -->
                    <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white mb-6 leading-[1.1]">
                        Kaindoa Pig Farm 
                        <span class="bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent dark:from-emerald-400 dark:to-teal-300 block mt-1">Management System</span>
                    </h1>

                    <!-- Swahili Product Narrative -->
                    <p class="text-base text-slate-500 dark:text-zinc-400 max-w-md mb-8 leading-relaxed">
                        Mfumo rahisi na wa kisasa wa kidijitali ulioundwa maalum kusaidia wafugaji kufuatilia breeds, afya ya mifugo, na uzalishaji wa kila siku wa shamba lenu kwa ufanisi zaidi.
                    </p>

                    <!-- Primary Routing Actions Area -->
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-4">
                        <template v-if="!$page.props.auth.user">
                            <Link
                                :href="route('login')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm"
                            >
                                Anza Sasa (Get Started)
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('dashboard')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 transition text-sm"
                            >
                                Ingia Kwenye Mfumo &rarr;
                            </Link>
                        </template>
                    </div>

                </div>

                <!-- Right Hand System Telemetry Mock Dashboard Preview -->
                <div class="lg:col-span-7 w-full">
                    <div class="w-full rounded-2xl border border-slate-200/80 bg-white p-3 shadow-xl shadow-slate-200/50 dark:border-zinc-850 dark:bg-zinc-900 dark:shadow-none">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-6 dark:border-zinc-800/50 dark:bg-zinc-950/40">
                            
                            <!-- Application Framing Title Headers -->
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-4 mb-6 dark:border-zinc-800">
                                <div class="flex items-center space-x-2">
                                    <span class="size-2.5 rounded-full bg-red-400"></span>
                                    <span class="size-2.5 rounded-full bg-yellow-400"></span>
                                    <span class="size-2.5 rounded-full bg-green-400"></span>
                                    <span class="text-[11px] font-mono font-medium text-slate-400 dark:text-zinc-500 pl-2">KPFM_CORE_DASHBOARD.EXE</span>
                                </div>
                                <span class="text-[10px] font-mono bg-slate-200/60 text-slate-600 px-2 py-0.5 rounded dark:bg-zinc-800 dark:text-zinc-400">Live Preview</span>
                            </div>

                            <!-- Real-time metrics grid inside bento framework -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                
                                <!-- Card Item: Breeds -->
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between h-32">
                                    <div class="flex justify-between items-start">
                                        <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 text-base">🐖</div>
                                        <span class="text-[10px] font-mono text-emerald-600 bg-emerald-500/10 px-1.5 py-0.5 rounded">Active</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 dark:text-zinc-300">Breeds Status</div>
                                        <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">Usimamizi wa koo na vizazi vya mifugo.</p>
                                    </div>
                                </div>

                                <!-- Card Item: Health Tracker -->
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between h-32">
                                    <div class="flex justify-between items-start">
                                        <div class="p-2 rounded-lg bg-blue-500/10 text-blue-600 text-base">🩺</div>
                                        <span class="text-[10px] font-mono text-blue-600 bg-blue-500/10 px-1.5 py-0.5 rounded">Secure</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 dark:text-zinc-300">Health Tracking</div>
                                        <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">Matibabu, rekodi na chanjo za mifugo.</p>
                                    </div>
                                </div>

                                <!-- Card Item: Records -->
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800/80 shadow-sm flex flex-col justify-between h-32">
                                    <div class="flex justify-between items-start">
                                        <div class="p-2 rounded-lg bg-amber-500/10 text-amber-600 text-base">📊</div>
                                        <span class="text-[10px] font-mono text-amber-600 bg-amber-500/10 px-1.5 py-0.5 rounded">Automated</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800 dark:text-zinc-300">Daily Records</div>
                                        <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">Uzalishaji na takwimu za shamba lako.</p>
                                    </div>
                                </div>

                            </div>

                            <!-- Secondary Simulated Operational Monitoring Data Bar -->
                            <div class="mt-4 p-3 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800/80 flex items-center justify-between text-[11px] font-mono text-slate-400 dark:text-zinc-500">
                                <div class="flex items-center space-x-2">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Database sync pipeline: OK</span>
                                </div>
                                <span class="hidden sm:inline">Records processed today: 100%</span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </main>

        <!-- Static System Footer Container -->
        <footer class="w-full border-t border-slate-200/60 bg-white dark:border-zinc-800/60 dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 dark:text-zinc-500 gap-4">
                <div>
                    &copy; {{ new Date().getFullYear() }} Kaindoa Pig Farm Management (KPFM). Haki zote zimehifadhiwa.
                </div>
                <div class="flex items-center space-x-2 font-mono text-[11px] bg-slate-100 dark:bg-zinc-900 px-3 py-1 rounded-md">
                    <span>Laravel v{{ laravelVersion }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">|</span>
                    <span>PHP v{{ phpVersion }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>