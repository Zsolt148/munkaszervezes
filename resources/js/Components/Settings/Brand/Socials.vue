<template>
    <v-card class="mt-4" outlined>
        <v-form @submit.prevent="saveSocials()">
            <v-row class="ma-0 pa-0">
                <v-col cols="12">
                    <v-card-title class="pt-1 ps-2 mb-0 ">
                        {{ trans('Social links') }}
                    </v-card-title>

                    <v-card-subtitle class="ps-2 mb-0 pb-3">
                        {{ trans('Social links for your business, often used in the theme footer') }}
                    </v-card-subtitle>

                    <v-divider/>

                    <v-text-field
                        v-model="form.facebook"
                        class="px-2 mb-0 pb-0 pt-4"
                        label="Facebook"
                        prepend-inner-icon="mdi-facebook"
                        :error-messages="form.errors.facebook"
                        dense
                        filled
                    ></v-text-field>

                    <v-text-field
                        v-model="form.instagram"
                        class="px-2 mb-0 pb-0"
                        label="Instagram"
                        prepend-inner-icon="mdi-instagram"
                        :error-messages="form.errors.instagram"
                        dense
                        filled
                    ></v-text-field>

                    <v-text-field
                        v-model="form.twitter"
                        class="px-2 mb-0 pb-0"
                        label="Twitter"
                        :error-messages="form.errors.twitter"
                        prepend-inner-icon="mdi-twitter"
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
    name: "Socials",
    inject: ['settings'],
    data(){
        return{
            form: this.$inertia.form({
                facebook: this.settings.facebook_link,
                instagram: this.settings.instagram_link,
                twitter: this.settings.twitter_link,
            }),
        }
    },
    methods: {
        saveSocials(){
            this.form.put(this.route('settings.saveSocials'),{
                preserveScroll: true,
            })
        }
    },
}
</script>