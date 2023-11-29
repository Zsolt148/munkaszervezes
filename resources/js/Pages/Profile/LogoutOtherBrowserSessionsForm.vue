<template>
    <form-section>
        <template #title>
            {{trans('Browser Sessions')}}
        </template>

        <template #description>
            {{trans('Manage and log out your active sessions on other browsers and devices.')}}
        </template>

        <template #form>
            <div class="text-base">
                {{trans("If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.")}}
            </div>

            <!-- Other Browser Sessions -->
            <div class="mt-5" v-if="sessions.length > 0">
                <v-row v-for="(session, i) in sessions" :key="i">
                    <v-col>
                        <v-icon v-if="session.agent.is_desktop">mdi-monitor</v-icon>
                        <div class="text-sm">
                            {{ session.agent.platform }} - {{ session.agent.browser }}
                        </div>

                        <div>
                            <div class="text-xs">
                                {{ session.ip_address }},

                                <span v-if="session.is_current_device">{{trans('This device')}}</span>
                                <span v-else>{{trans('Last active')}} {{ session.last_active }}</span>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </div>
        </template>

        <template #actions>
            <confirms-password @confirmed="logoutOtherBrowserSessions" :must-confirm="true">
                <v-btn color="primary" >
                    {{trans('Log out other browser sessions')}}
                </v-btn>
            </confirms-password>
        </template>
    </form-section>
</template>

<script>
    import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";
    import FormSection from "@/Components/Shared/FormSection.vue";

    export default {
        name: "LogoutOtherBrowserSessionsForm",

        props: ['sessions'],

        components: {
            FormSection,
            ConfirmsPassword,
        },

        data() {
            return {
                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            logoutOtherBrowserSessions(password) {
                this.form.password = password
                this.form.post(this.route('other-browser-sessions.destroy'), {
                    preserveScroll: true,
                    //onSuccess: () => this.closeModal(),
                    onFinish: () => this.form.reset(),
                })
            },
        },
    }
</script>
