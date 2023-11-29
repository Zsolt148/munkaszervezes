<template>
    <v-app :style="{background: $vuetify.theme.dark  ? $vuetify.theme.themes.dark.background : $vuetify.theme.themes.light.background}" id="inspire">
        <transition name="page" mode="out-in" appear>
            <v-main>
                <v-container>
                    <slot />
                </v-container>
            </v-main>
        </transition>
    </v-app>
</template>

<script>
export default {
    name: "ErrorLayout",
    components: {

    },
};
</script>

<style>
    .page-enter-active,
    .page-leave-active {
        transition: all 0.3s;
    }

    .page-enter,
    .page-leave-active {
        opacity: 0;
    }
</style>
