<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { FloatLabel, InputText, Message } from 'primevue';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <FloatLabel variant="on" class="mt-4">
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

            <div>
                <FloatLabel variant="on">
                    <InputText
                        type="email"
                        id="on_email"
                        v-model="form.email"
                        class="w-full"
                        :invalid="!!form.errors.email"
                    />
                    <label for="on_email">Email</label>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
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
