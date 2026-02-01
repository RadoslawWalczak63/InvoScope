<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

import { PageProps } from '@/types';
import { Avatar, Button, Drawer, Menu, Popover } from 'primevue';
import { MenuItem } from 'primevue/menuitem';
import Swal from 'sweetalert2';

interface FlashMessage {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

interface CustomPageProps extends PageProps {
    flash?: FlashMessage;
}

const showingMobileMenu = ref(false);
const page = usePage<CustomPageProps>();
const user = page.props.auth.user;

const navItems = [
    {
        label: 'Dashboard',
        icon: 'pi pi-home',
        route: 'dashboard',
        url: route('dashboard'),
    },
    {
        label: 'Entities',
        icon: 'pi pi-users',
        route: 'entities.index',
        url: route('entities.index'),
    },
    {
        label: 'Invoices',
        icon: 'pi pi-receipt',
        route: 'invoices.index',
        url: route('invoices.index'),
    },
    {
        label: 'Queued Jobs',
        icon: 'pi pi-clock',
        route: 'queued-jobs.index',
        url: route('queued-jobs.index'),
    },
];

const userNavItems: MenuItem[] = [
    {
        label: 'Profile',
        icon: 'pi pi-user',
        url: route('profile.edit'),
    },
    {
        label: 'Logout',
        icon: 'pi pi-sign-out',
        url: route('logout'),
    },
];

const notificationsMenu = ref();
const toggleNotifications = (event: Event) => {
    notificationsMenu.value.toggle(event);
};

const userMenu = ref();
const toggleUserMenu = (event: Event) => {
    userMenu.value.toggle(event);
};

const messageIcon = computed(() => {
    if (page.props.flash?.success) return 'success';
    if (page.props.flash?.error) return 'error';
    if (page.props.flash?.warning) return 'warning';
    if (page.props.flash?.info) return 'info';
    return undefined;
});

const message = computed(() => {
    return (
        page.props.flash?.success ||
        page.props.flash?.error ||
        page.props.flash?.warning ||
        page.props.flash?.info ||
        ''
    );
});

watch(
    () => page.props.flash,
    () => {
        if (message.value) {
            Swal.fire({
                text: message.value,
                icon: messageIcon.value,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            });
        }
    },
    {
        deep: true,
        immediate: true,
    },
);
</script>

<template>
    <div
        class="bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-0 flex min-h-screen flex-col font-sans transition-colors duration-200 md:flex-row"
    >
        <aside
            class="dark:bg-surface-900 border-surface-200 dark:border-surface-800 sticky top-0 z-20 hidden h-screen w-64 shrink-0 flex-col border-r bg-white md:flex"
        >
            <div
                class="border-surface-100 dark:border-surface-800 flex h-16 items-center justify-center border-b px-6"
            >
                <ApplicationLogo />
            </div>

            <div class="flex-1 space-y-1 overflow-y-auto">
                <Menu :model="navItems" class="!border-0">
                    <template #item="{ item, props }">
                        <Link
                            :href="item.url ?? '#'"
                            class="flex items-center gap-2 border-l-2 border-transparent transition-colors duration-300 ease-in-out"
                            :class="{
                                'border-blue-500': route().current(item.route),
                            }"
                            v-bind="props.action"
                        >
                            <span :class="item.icon" />
                            <span class="font-medium">{{ item.label }}</span>
                            <Badge
                                v-if="item.badge"
                                class="ml-auto"
                                :value="item.badge"
                            />
                            <span
                                v-if="item.shortcut"
                                class="border-surface bg-emphasis text-muted-color ml-auto rounded border p-1 text-xs"
                                >{{ item.shortcut }}</span
                            >
                        </Link>
                    </template>
                </Menu>
            </div>

            <div
                class="border-surface-200 dark:border-surface-800 border-t p-4"
            >
                <button
                    dusk="user-menu-button"
                    @click="toggleUserMenu"
                    class="hover:bg-surface-100 dark:hover:bg-surface-800 group flex w-full items-center gap-3 rounded-lg p-2 text-left transition-colors duration-200"
                >
                    <Avatar
                        :label="user.name.charAt(0)"
                        shape="circle"
                        class="bg-primary-500 shrink-0 text-white"
                    />
                    <div class="flex-1 overflow-hidden">
                        <div
                            class="text-surface-900 dark:text-surface-100 truncate text-sm font-semibold"
                        >
                            {{ user.name }}
                        </div>
                        <div
                            class="text-surface-500 dark:text-surface-400 truncate text-xs"
                        >
                            {{ user.email }}
                        </div>
                    </div>
                    <i
                        class="pi pi-chevron-right text-surface-400 group-hover:text-surface-600 dark:text-surface-500 text-xs"
                    ></i>
                </button>
            </div>
        </aside>

        <Drawer v-model:visible="showingMobileMenu" header="Menu" class="!w-64">
            <template #header>
                <div class="flex items-center gap-2">
                    <ApplicationLogo />
                </div>
            </template>

            <nav class="flex flex-col gap-2">
                <Menu :model="navItems" class="!border-0">
                    <template #item="{ item, props }">
                        <Link
                            :href="item.url ?? '#'"
                            class="flex items-center gap-2 border-l-2 border-transparent transition-colors duration-300 ease-in-out"
                            :class="{
                                'border-blue-500': route().current(item.route),
                            }"
                            v-bind="props.action"
                        >
                            <span :class="item.icon" />
                            <span class="font-medium">{{ item.label }}</span>
                            <Badge
                                v-if="item.badge"
                                class="ml-auto"
                                :value="item.badge"
                            />
                            <span
                                v-if="item.shortcut"
                                class="border-surface bg-emphasis text-muted-color ml-auto rounded border p-1 text-xs"
                            >
                                {{ item.shortcut }}
                            </span>
                        </Link>
                    </template>
                </Menu>
            </nav>
        </Drawer>

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <header
                class="dark:bg-surface-900 border-surface-200 dark:border-surface-800 sticky top-0 z-10 flex h-16 items-center justify-between border-b bg-white px-4 sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-4">
                    <div class="md:hidden">
                        <Button
                            icon="pi pi-bars"
                            text
                            rounded
                            severity="secondary"
                            @click="showingMobileMenu = true"
                            aria-label="Open Menu"
                        />
                    </div>

                    <div
                        v-if="$slots.header"
                        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        <slot name="header" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Button
                        icon="pi pi-bell"
                        text
                        rounded
                        severity="secondary"
                        aria-label="Notifications"
                        @click="toggleNotifications"
                    />

                    <div class="relative md:hidden">
                        <Avatar
                            :label="user.name.charAt(0)"
                            shape="circle"
                            class="bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-100 cursor-pointer"
                            @click="toggleUserMenu"
                        />
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4">
                <slot />
            </main>
        </div>

        <Menu ref="userMenu" :model="userNavItems" popup id="user-menu">
            <template #item="{ item, props }">
                <Link
                    :href="item.url ?? '#'"
                    class="flex w-full items-center gap-2 px-3 py-2"
                    v-bind="props.action"
                >
                    <span :class="item.icon" />
                    <span>{{ item.label }}</span>
                </Link>
            </template>
        </Menu>

        <Popover ref="notificationsMenu" id="notifications-menu">
            <div class="flex w-80 flex-col">
                <div
                    class="flex min-h-[100px] flex-col items-center justify-center text-center"
                >
                    <i
                        class="pi pi-bell-slash text-surface-400 dark:text-surface-500 mb-2 text-2xl"
                    >
                    </i>
                    <p class="text-surface-500 dark:text-surface-400 text-sm">
                        No new notifications.
                    </p>
                </div>
            </div>
        </Popover>
    </div>
</template>
