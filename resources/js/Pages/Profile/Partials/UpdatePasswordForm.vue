<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Update Password
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ensure your account is using a long, random password to stay
                secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <FloatLabel variant="on" class="mt-4">
                    <InputText
                        type="password"
                        id="on_current_password"
                        v-model="form.current_password"
                        class="w-full"
                        :invalid="!!form.errors.current_password"
                    />
                    <label for="on_current_password">Current Password</label>
                </FloatLabel>

                <Message
                    severity="error"
                    variant="simple"
                    v-if="form.errors.current_password"
                    class="mt-2"
                >
                    {{ form.errors.current_password }}
                </Message>
            </div>

            <div>
                <FloatLabel variant="on" class="mt-4">
                    <InputText
                        type="password"
                        id="on_password"
                        v-model="form.password"
                        class="w-full"
                        :invalid="!!form.errors.password"
                    />
                    <label for="on_password">New Password</label>
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

            <div>
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

            <div class="flex items-center gap-4">
                <Button
                    type="submit"
                    :disabled="form.processing"
                    label="Save"
                />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
