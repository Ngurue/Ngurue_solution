<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Create Account" />

    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 dark:bg-zinc-950">
        <!-- Main Registration Card -->
        <div class="w-full max-w-[480px] rounded-2xl border border-slate-200/80 bg-white p-8 shadow-sm dark:border-zinc-850 dark:bg-zinc-900">
            
            <!-- Branding & Header -->
            <div class="mb-8 flex flex-col items-center text-center">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-lg">
                    S
                </div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-zinc-50">
                    Get Started
                </h2>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-zinc-400">
                    Create your system workspace account
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-5">
                <!-- Full Name -->
                <div>
                    <InputLabel for="name" value="Full Name" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                    <div class="mt-1.5">
                        <TextInput
                            id="name"
                            type="text"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="John Doe"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.name" />
                </div>

                <!-- Email Address -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                    <div class="mt-1.5">
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="name@example.com"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.email" />
                </div>

                <!-- Passwords Grid Layout -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="Password" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                        <div class="mt-1.5">
                            <TextInput
                                id="password"
                                type="password"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                        </div>
                        <InputError class="mt-1.5" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                        <div class="mt-1.5">
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                        </div>
                        <InputError class="mt-1.5" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <!-- Submit Button Area -->
                <div class="pt-2">
                    <PrimaryButton
                        class="flex w-full justify-center rounded-xl bg-emerald-600 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Creating account...' : 'Create Account' }}
                    </PrimaryButton>
                </div>
            </form>

            <!-- Return to Login Footer -->
            <div class="mt-6 border-t border-slate-100 pt-4 text-center text-sm dark:border-zinc-800/60">
                <span class="text-slate-500 dark:text-zinc-400">Already registered?</span>
                <Link :href="route('login')" class="ms-1 font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">
                    Sign in instead
                </Link>
            </div>

        </div>
    </div>
</template>