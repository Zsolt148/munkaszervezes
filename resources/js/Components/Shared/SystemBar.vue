<template>
    <v-system-bar
        v-if="$page.props.page.maintenance"
        color="red"
        app
        dark
        height="30"
    >
    <div class="w-full text-center">
        <v-icon class="me-2">mdi-alert</v-icon>
        <span>Page is under maintenance</span>
    </div>
    </v-system-bar> 
</template>