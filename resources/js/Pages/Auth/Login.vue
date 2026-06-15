<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign In" />

    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 dark:bg-zinc-950">
        <!-- Main Login Card -->
        <div class="w-full max-w-[440px] rounded-2xl border border-slate-200/80 bg-white p-8 shadow-sm dark:border-zinc-850 dark:bg-zinc-900">
            
            <!-- Branding Logo & Header -->
            <div class="mb-8 flex flex-col items-center text-center">
                <div class="mb-4 flex size-10 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold text-lg">
                    S
                </div>
                <h2 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-zinc-50">
                    Welcome Back
                </h2>
                <p class="mt-1.5 text-sm text-slate-500 dark:text-zinc-400">
                    Enter your credentials to access your dashboard
                </p>
            </div>

            <!-- Session Status -->
            <div v-if="status" class="mb-6 rounded-lg bg-emerald-50 p-3 text-sm font-medium text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                {{ status }}
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-5">
                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Email Address" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                    <div class="mt-1.5">
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="name@example.com"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between">
                        <InputLabel for="password" value="Password" class="text-xs font-medium text-slate-600 dark:text-zinc-400" />
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-xs font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300"
                        >
                            Forgot?
                        </Link>
                    </div>
                    <div class="mt-1.5">
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full rounded-xl border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100 dark:focus:bg-zinc-950"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <InputError class="mt-1.5" :message="form.errors.password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-zinc-800 dark:bg-zinc-950" />
                        <span class="ms-2 text-sm text-slate-500 dark:text-zinc-400">Keep me signed in</span>
                    </label>
                </div>

                <!-- Action Button -->
                <div class="pt-1">
                    <PrimaryButton
                        class="flex w-full justify-center rounded-xl bg-emerald-600 py-3 text-center text-sm font-semibold text-white transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </PrimaryButton>
                </div>
            </form>

            <!-- Register Link Footer -->
            <div class="mt-6 border-t border-slate-100 pt-4 text-center text-sm dark:border-zinc-800/60">
                <span class="text-slate-500 dark:text-zinc-400">New to the platform?</span>
                <Link :href="route('register')" class="ms-1 font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">
                    Create an account
                </Link>
            </div>

        </div>
    </div>
</template>