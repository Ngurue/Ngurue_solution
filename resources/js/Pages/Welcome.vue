<script setup>
import { Head, Link } from '@inertiajs/vue3';
// Import your logo asset so Vite compiles the path correctly
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
        
        <div class="absolute inset-0 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:32px_32px] opacity-[0.03] dark:opacity-[0.05] pointer-events-none"></div>

        <header class="sticky top-0 z-50 w-full border-b border-slate-200/60 bg-white/80 backdrop-blur-md dark:border-zinc-800/60 dark:bg-zinc-950/80">
            <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
                
                <div class="flex items-center space-x-3">
                    <img 
                        :src="logoAsset" 
                        alt="KPFM Logo" 
                        class="h-9 w-auto object-contain rounded-lg"
                        @error="(e) => e.target.style.display = 'none'" 
                    />
                    <span class="text-base font-semibold tracking-tight text-slate-900 dark:text-zinc-50">
                        Kaindoa Pig Farm <span class="text-emerald-600 dark:text-emerald-400 font-medium">Management</span>
                    </span>
                </div>

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

        <main class="flex-1 flex flex-col justify-center items-center py-16 px-6 relative z-10">
            <div class="max-w-3xl mx-auto text-center flex flex-col items-center">
                
              <!-- Central Logo & Brand Showcase Block -->
<div class="flex flex-col items-center justify-center mb-8 w-full max-w-md mx-auto">
    
    <!-- Large High-Visibility Logo Display -->
    <div class="relative p-4 rounded-2xl bg-white/40 dark:bg-zinc-900/20 backdrop-blur-sm border border-slate-200/40 dark:border-zinc-800/40 shadow-sm transition hover:scale-[1.02] duration-300">
        <img 
            src="https://res.cloudinary.com/do3i7dfhc/image/upload/v1781540896/logonew_nqg9zo.png" 
            alt="KPFM Main Logo" 
            class="h-28 sm:h-36 w-auto min-w-[160px] max-w-[280px] object-contain drop-shadow-md" 
        />
    </div>

    <!-- Clean Status Pill Separated Below the Large Logo -->
    <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs font-medium tracking-wide">
        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="font-mono uppercase text-[10px] tracking-wider">KPFM System Live • v1.0</span>
    </div>

</div>

                <h1 class="text-4xl sm:text-6xl font-bold tracking-tight text-slate-900 dark:text-white mb-6 leading-[1.1]">
                   Kaindoa Pig Farm Management 
                    <span class="text-emerald-600 dark:text-emerald-400 block sm:inline">(KPFM)</span>
                </h1>

              

                <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-4">
                    <template v-if="!$page.props.auth.user">
                        <Link
                            :href="route('login')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-600/10 hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-950 transition text-sm"
                        >
                            Anza Sasa (Get Started)
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('dashboard')"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-600/10 hover:bg-emerald-500 transition text-sm"
                        >
                            Ingia Kwenye Mfumo &rarr;
                        </Link>
                    </template>
                </div>

                <div class="mt-16 w-full max-w-4xl rounded-2xl border border-slate-200/80 bg-white p-2 shadow-xl shadow-slate-100/70 dark:border-zinc-850 dark:bg-zinc-900 dark:shadow-none">
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 px-6 py-12 dark:border-zinc-800 dark:bg-zinc-950/50">
                        <div class="flex flex-col items-center justify-center">
                            
                            <img :src="logoAsset" alt="KPFM Framework" class="h-12 w-auto mb-4 object-contain opacity-25 grayscale dark:invert" />
                            
                            <span class="text-[11px] font-mono tracking-widest uppercase text-slate-400 dark:text-zinc-600">System Operations Dashboard Preview</span>
                            
                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 text-left w-full max-w-xl">
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                                    <div class="text-lg">🐖</div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-zinc-300 mt-1">Breeds Status</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Usimamizi wa koo na vizazi</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                                    <div class="text-lg">🩺</div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-zinc-300 mt-1">Health Tracking</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Matibabu na chanjo za mifugo</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                                    <div class="text-lg">📊</div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-zinc-300 mt-1">Daily Records</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Uzalishaji na takwimu za shamba</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </main>

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