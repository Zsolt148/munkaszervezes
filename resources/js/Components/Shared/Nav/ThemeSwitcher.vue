<template>
    <v-fade-transition mode="out-in">
        <v-icon
            :key="$vuetify.theme.dark"
            @click="$vuetify.theme.dark = !$vuetify.theme.dark"
        >
            {{
                $vuetify.theme.dark
                    ? 'mdi-weather-sunny'
                    : 'mdi-weather-night'
            }}
        </v-icon>
    </v-fade-transition>
</template>