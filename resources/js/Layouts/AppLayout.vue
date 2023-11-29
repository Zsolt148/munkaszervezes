<template>
    <v-app id="inspire">
        <navbar-inner></navbar-inner>
        <v-main>
            <v-container class="px-5 px-md-10" fluid>
                <flash-messages :flash="$page.props.flash" :errors="$page.props.errors" />
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import FlashMessages from "@/Components/Shared/FlashMessages.vue";
import NavbarInner from "@/Components/Shared/Nav/NavbarInner.vue";

export default {
    name: "AppLayout",
    components: {
        FlashMessages,
        NavbarInner,
    },
};
</script>
