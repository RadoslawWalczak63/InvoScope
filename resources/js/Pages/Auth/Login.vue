<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <FloatLabel variant="on">
                    <InputText
                        type="email"
                        id="on_email"
                        v-model="form.email"
                        class="w-full"
                        :invalid="!!form.errors.email"
                        name="email"
                    />
                    <label for="on_password">Email</label>
                </FloatLabel>

                <Message
                    severity="error"
                    variant="simple"
                    v-if="form.errors.email"
                    class="mt-2"
                >
                    {{ form.errors.email }}
                </Message>
            </div>

            <div class="mt-4">
                <FloatLabel variant="on" class="mt-4">
                    <InputText
                        type="password"
                        name="password"
                        id="on_password"
                        v-model="form.password"
                        class="w-full"
                        :invalid="!!form.errors.password"
                    />
                    <label for="on_password">Password</label>
                </FloatLabel>

                <Message
                    severity="error"
                    variant="simple"
                    v-if="form.errors.password"
                    class="mt-2"
                >
                    {{ form.errors.password }}
                </Message>
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model="form.remember" binary />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                        Remember me
                    </span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <Button
                    type="submit"
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </Button>
            </div>
        </form>

        <template #footer>
            <div
                class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400"
            >
                <span>Don't have an account?</span>
                <Link
                    :href="route('register')"
                    class="ml-1 font-medium text-blue-500 hover:underline"
                >
                    Create one
                </Link>
            </div>
        </template>
    </GuestLayout>
</template>
