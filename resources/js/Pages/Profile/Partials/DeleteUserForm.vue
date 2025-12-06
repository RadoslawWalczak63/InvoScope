<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { Button, FloatLabel, InputText, Message } from 'primevue';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <Button
            severity="danger"
            @click="confirmUserDeletion"
            label="Delete Account"
        />

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
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

                <div class="mt-6 flex justify-end gap-2">
                    <Button
                        severity="secondary"
                        @click="closeModal"
                        label="Cancel"
                    />

                    <Button
                        severity="danger"
                        :disabled="form.processing"
                        @click="deleteUser"
                        label="Delete Account"
                    />
                </div>
            </div>
        </Modal>
    </section>
</template>
