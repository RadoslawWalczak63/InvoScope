<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

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

            <div class="mt-4">
                <FloatLabel variant="on">
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
                <FloatLabel variant="on">
                    <InputText
                        type="password"
                        id="on_password_confirmation"
                        v-model="form.password_confirmation"
                        class="w-full"
                        :invalid="!!form.errors.password_confirmation"
                    />
                    <label for="on_password_confirmation">
                        Confirm Password</label
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
                <Button
                    type="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
