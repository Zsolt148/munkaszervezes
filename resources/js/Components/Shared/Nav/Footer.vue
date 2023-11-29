<template>
    <v-footer inset height="56" class="px-0" app>
        <div class="boxed-container w-full">
            <div class="mx-6 d-flex justify-center">
                <span class="ml-2">
                    {{ getVersion() }}
                </span>
                <span class="ms-2">
                    &copy; {{ new Date().getFullYear() }} -
                    <a class="text-decoration-none" target="_blank">XtraDevelopers</a>
                </span>
                <div class="ms-4" v-show="hasRole('superadmin')">
                    <v-icon small>mdi-console</v-icon>
                    <span class="ms-1">
                        <a class="text-decoration-none" :href="route('devops.horizon')" target="_blank">Horizon</a>
                    </span>
                    <span class="ms-1" v-show="$page.props.page.telescope">
                        <a class="text-decoration-none" :href="route('devops.telescope')" target="_blank">Telescope</a>
                    </span>
                </div>
            </div>
        </div>
    </v-footer>
</template>