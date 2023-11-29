<template>
    <v-app>
        <v-main class="auth--container">
            <v-container class="d-flex h-full align-center justify-center">
                <flash-messages :flash="$page.props.flash" />
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
import FlashMessages from "@/Components/Shared/FlashMessages";
export default {
    name: "AuthLayout",
    components: {
        FlashMessages
    }
}
</script>
