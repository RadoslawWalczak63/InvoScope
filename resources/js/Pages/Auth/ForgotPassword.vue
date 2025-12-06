<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
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

            <div class="mt-4 flex items-center justify-end">
                <Button
                    type="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
