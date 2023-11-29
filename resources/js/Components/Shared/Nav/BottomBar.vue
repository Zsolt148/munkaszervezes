<template>
    <v-bottom-navigation : class="nav d-fixed d-md-fixed d-lg-none" fixed>
        <v-btn
            :class="{'v-btn--active': isUrl('/dashboard')}"
            @click="$inertia.visit(route('dashboard'))">
            <span class="mt-1">{{trans('Dashboard')}}</span>

            <v-icon class="mx-auto">
                mdi-view-dashboard
            </v-icon>
        </v-btn>

        <v-btn
            v-if="hasRole('superadmin')"
            :class="{'v-btn--active': isUrl('/admins')}"
            @click="$inertia.visit(route('admins.index'))">
            <span class="mt-1">{{trans('Admins')}}</span>

            <v-icon class="mx-auto">
                mdi-shield-crown
            </v-icon>
        </v-btn>

        <v-btn
            :class="{'v-btn--active': isUrl('/tasks')}"
            @click="$inertia.visit(route('tasks.index'))">
            <span class="mt-1">{{trans('Task manager')}}</span>

            <v-icon class="mx-auto">
                mdi-checkbox-multiple-marked
            </v-icon>
        </v-btn>

        <v-btn
            v-if="hasRole('superadmin') || hasPermission('view logs')"
            :class="{'v-btn--active': isUrl('/logs')}"
            @click="$inertia.visit(route('logs.index'))">
            <span class="mt-1">{{trans('Logs')}}</span>

            <v-icon class="mx-auto">
                mdi-note-multiple
            </v-icon>
        </v-btn>

        <v-btn
            v-if="hasRole('superadmin') || hasPermission('update_settings')"
            :class="{'v-btn--active': isUrl('/settigs')}"
            @click="$inertia.visit(route('settings.index'))">
            <span class="mt-1">{{trans('Settings')}}</span>

            <v-icon class="mx-auto">
                mdi-cog
            </v-icon>
        </v-btn>

        <confirms-modal @confirmed="logout">
            <v-btn class="pt-2">
                <span class="mt-1">{{trans('Logout')}}</span>
                <v-icon class="mx-auto">
                    mdi-logout
                </v-icon>
            </v-btn>
        </confirms-modal>
    </v-bottom-navigation>
</template>

<script>
import ConfirmsModal from '../ConfirmsModal.vue'

export default {
    data(){
        return{
            lang: this.$page.props.auth.user.locale ?? 'hu',
            logoutForm: this.$inertia.form({
                method: '_POST',
            })
        }
    },
    components:{
        ConfirmsModal
    },
    methods:{
        changeLanguage(){
            this.$inertia.visit(this.route('language', this.lang))
        },
        logout() {
            this.logoutForm.post(this.route('logout'))
        },
    },
    computed: {
        navigationStyle() {
            if (this.$vuetify.breakpoint.smAndDown) return { width: '100%' }
            else return
        }
    }
}
</script>

<style lang="scss" scoped>
.v-list-item__title{
    font-size: 10px!important;
}

.nav{
    .v-btn__content{
        span{
            font-size: 10px!important;
        }
    }
}
</style>