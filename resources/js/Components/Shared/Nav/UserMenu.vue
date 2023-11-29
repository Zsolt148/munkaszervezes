<template>
    <v-menu offset-y left nudge-bottom="14" min-width="230" content-class="user-profile-menu-content">
        <template v-slot:activator="{ on, attrs }">
            <v-badge id="user--menu-button" bottom color="success" overlap offset-x="12" offset-y="12" class="ms-4" dot>
                <v-avatar size="40px" v-bind="attrs" v-on="on">
                    <v-img :src="getProfPicture($page.props.auth.user.profile_photo_url, $page.props.auth.user.name)"></v-img>
                </v-avatar>
            </v-badge>
        </template>
        <v-list dense>
            <div class="pb-3 pt-2">
                <v-badge bottom color="success" overlap offset-x="12" offset-y="12" class="ms-4" dot>
                    <v-avatar size="40px">
                        <v-img :src="getProfPicture($page.props.auth.user.profile_photo_url, $page.props.auth.user.name)"></v-img>
                    </v-avatar>
                </v-badge>
                <div class="d-inline-flex flex-column justify-center ms-3" style="vertical-align: middle">
                    <span class="text--primary font-weight-semibold mb-n1"> {{ $page.props.auth.user.name }} </span>
                    <small class="text--disabled text-capitalize">{{ $page.props.auth.roles_text }}</small>
                </div>
            </div>

            <v-divider></v-divider>

            <!-- Profile -->
            <v-list-item class="py-0" id="menu--profile" link @click="$inertia.visit(route('profile.show'))">
                <v-list-item-icon class="me-2">
                    <v-icon size="22">
                        mdi-account-outline
                    </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{ trans("Profile") }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

<!--            <v-list-item id="menu&#45;&#45;profile" link @click="$inertia.visit(route('profile.tempo'))">-->
<!--                <v-list-item-icon class="me-2">-->
<!--                    <v-icon size="22">-->
<!--                        mdi-checkbox-multiple-marked-outline-->
<!--                    </v-icon>-->
<!--                </v-list-item-icon>-->
<!--                <v-list-item-content>-->
<!--                    <v-list-item-title>{{ trans("Tempo") }}</v-list-item-title>-->
<!--                </v-list-item-content>-->
<!--            </v-list-item>-->

<!--            <v-divider></v-divider>-->

            <!-- Settings -->
<!--            <v-list-item class="py-0" link @click="$inertia.visit(route('settings.index'))">-->
<!--                <v-list-item-icon class="me-2">-->
<!--                    <v-icon>-->
<!--                        mdi-cog-outline-->
<!--                    </v-icon>-->
<!--                </v-list-item-icon>-->
<!--                <v-list-item-content>-->
<!--                    <v-list-item-title>{{ trans("Settings") }}</v-list-item-title>-->
<!--                </v-list-item-content>-->
<!--            </v-list-item>-->

            <v-divider></v-divider>

            <!-- Logout -->
            <v-list-item class="py-0" @click.prevent="logout">
                <v-list-item-icon class="me-2">
                    <v-icon>
                        mdi-logout-variant
                    </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{ trans("Logout") }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    name: "UserMenu",
    data() {
        return {
            logoutForm: this.$inertia.form({
                method: '_POST',
            })
        }
    },
    methods:{
        logout() {
            this.logoutForm.post(this.route('logout'))
        },
    }
}
</script>

<style lang="scss">
    .user-profile-menu-content {
        border: thin solid rgba(0, 0, 0, 0.12);
        .v-list-item {
            min-height: 2.5rem !important;
        }
    }
</style>
