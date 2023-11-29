<template>
    <v-card class="mt-4" outlined>
        <v-form @submit.prevent="saveDescription()">
            <v-row class="ma-0 pa-0">
                <v-col cols="12">
                    <v-card-title class="pt-1 ps-2 mb-0 ">
                        {{ trans('Short description') }}
                    </v-card-title>

                    <v-card-subtitle class="ps-2 mb-0 pb-3">
                        {{ trans('Description of your business often used in bios and listings') }}
                    </v-card-subtitle>

                    <v-divider class="pb-4" />

                    <language-select class="ps-2 mb-0 pb-0" :lang.sync="lang" :errors="form.errors" />

                    <v-textarea
                        outlined
                        v-model="form.short_description[lang]"
                        class="ps-2 mb-0 pb-0"
                        :error-messages="form.errors ? form.errors['short_description.' + lang] : null"
                        :label="trans('Short description') + ' - ' + lang"
                    ></v-textarea>

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
    name: "Description",
    inject: ['settings'],
    data() {
        return{
            lang: 'hu',
            form: this.$inertia.form({
                short_description: Object.assign({}, this.settings.short_description),
            }),
        }
    },
    methods: {
        saveDescription(){
            this.form.post(this.route('settings.saveDescription'), {
                preserveScroll: true,
            })
        }
    },
}
</script>
