<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <FloatLabel variant="on">
                    <InputText
                        type="text"
                        id="on_name"
                        v-model="form.name"
                        class="w-full"
                        :invalid="!!form.errors.name"
                    />
                    <label for="on_name">Name</label>
                </FloatLabel>

                <Message
                    severity="error"
                    variant="simple"
                    v-if="form.errors.name"
                    class="mt-2"
                >
                    {{ form.errors.name }}
                </Message>
            </div>

            <div class="mt-4">
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

            <div class="mt-4">
                <FloatLabel variant="on" class="mt-4">
                    <InputText
                        type="password"
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

            <div class="mt-4">
                <FloatLabel variant="on" class="mt-4">
                    <InputText
                        type="password"
                        id="on_password_confirmation"
                        v-model="form.password_confirmation"
                        class="w-full"
                        :invalid="!!form.errors.password_confirmation"
                    />
                    <label for="on_password_confirmation"
                        >Confirm Password</label
                    >
                </FloatLabel>

                <Message
                    severity="error"
                    variant="simple"
                    v-if="form.errors.password_confirmation"
                    class="mt-2"
                >
                    {{ form.errors.password_confirmation }}
                </Message>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <Button
                    type="submit"
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    label="Register"
                />
            </div>
        </form>
    </GuestLayout>
</template>
