<template>
    <form-section>
        <template #title>
            {{trans('Delete Account')}}
        </template>

        <template #description>
            {{trans('Permanently delete your account.')}}
        </template>

        <template #form>
            <div>
                {{trans('Once your account is deleted, all of its resources and data will be permanently deleted.Before deleting your account, please download any data or information that you wish to retain.')}}
            </div>
        </template>
        <template #actions>
            <confirms-password @confirmed="deleteUser" :must-confirm="true">
                <v-btn color="error" >
                    {{trans('Delete Account')}}
                </v-btn>
            </confirms-password>
        </template>
    </form-section>
</template>

<script>
import FormSection from "@/Components/Shared/FormSection.vue";
import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";

export default {
    components: {
        FormSection,
        ConfirmsPassword
    },

    data() {
        return {
            form: this.$inertia.form({
                password: '',
            })
        }
    },

    methods: {
        deleteUser() {
            this.form.delete(this.route('current-user.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    //
                },
                onFinish: () => this.form.reset(),
            })
        },
    },
}
</script>
