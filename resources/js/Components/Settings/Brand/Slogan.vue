<template>
    <v-card class="mt-4" outlined>
        <v-form @submit.prevent="saveSlogan()">
            <v-row class="ma-0 pa-0">
                <v-col cols="12">
                    <v-card-title class="pt-1 ps-2 mb-0 ">
                        {{ trans('Slogan') }}
                    </v-card-title>

                    <v-card-subtitle class="ps-2 mb-0 pb-3">
                        {{ trans('Brand statement or tagline often used along with your logo') }}
                    </v-card-subtitle>

                    <v-divider class="pb-4" />

                    <language-select class="ps-2 mb-0 pb-0" :lang.sync="lang" :errors="form.errors" />

                    <v-text-field
                        v-model="form.slogan[lang]"
                        class="ps-2 mb-0 pb-0"
                        name="slogan"
                        id="slogan"
                        :error-messages="form.errors ? form.errors['slogan.' + lang] : null"
                        :label="trans('Slogan of website') + ' - ' + lang"
                        dense
                        filled
                    ></v-text-field>

                    <v-btn type="submit" class="mx-2 mt-0 float-right" right color="primary"  :loading="form.processing">
                        <v-icon left>
                            mdi-content-save
                        </v-icon>
                        {{trans("Save")}}
                    </v-btn>
                </v-col>
            </v-row>
        </v-form>
    </v-card>
</template>

<script>
export default {
    name: "Slogan",
    inject: ['settings'],
    data() {
        return{
            lang: 'hu',
            form: this.$inertia.form({
                slogan: Object.assign({}, this.settings.slogan),
            }),
        }
    },
    methods: {
        saveSlogan() {
            this.form.post(this.route('settings.saveSlogan'), {
                preserveScroll: true
            })
        }
    },
}
</script>
