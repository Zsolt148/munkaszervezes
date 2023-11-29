<template>
    <v-card style="overflow:hidden" class="mt-4" outlined>
        <v-row>
            <v-col cols=6>
                <v-form @submit.prevent="saveLogo()">
                    <v-row class="ma-0 pa-0">
                        <v-col cols="12">
                            <v-card-title class="pt-1 ps-2 mb-0 ">
                                {{ trans('Upload your logo') }}
                            </v-card-title>

                            <v-card-subtitle class="ps-2 mb-0 pb-3">
                                {{ trans("Uploaded logo will displayed in the admin panel and will be visible in the website's header") }}
                            </v-card-subtitle>

                            <v-divider/>

                            <div v-if="settings.logo" class="d-flex align-end">
                                <v-img height="100px" class="mb-4" contain :src="settings.logo" />
                            </div>

                            <v-file-input
                                v-model="logoForm.file"
                                :label="trans('Select your logo')"
                                :placeholder="trans('Select your logo')"
                                class="mr-2 pt-4"
                                prepend-icon="mdi-paperclip"
                                filled
                                :show-size="1000"
                            >
                                <template v-slot:selection="{ index, text }">
                                <v-chip
                                    v-if="index < 2"
                                    color="primary"
                                    dark
                                    label
                                    small
                                >
                                    {{ text }}
                                </v-chip>
                                </template>
                            </v-file-input>

                            <v-btn type="submit" class="mx-2 mt-0 float-right" right color="primary"  :loading="logoForm.processing">
                                <v-icon left>
                                    mdi-content-save
                                </v-icon>
                                {{trans("Save")}}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>

            <v-divider vertical class='p-4 height-full width-100'/>

            <v-col cols="6">
                <v-form @submit.prevent="saveFavicon()">
                    <v-row class="ma-0 pa-0">
                        <v-col cols="12">
                            <v-card-title class="pt-1 ps-2 mb-0 ">
                                {{ trans('Upload your icon') }}
                            </v-card-title>

                            <v-card-subtitle class="ps-2 mb-0 pb-3">
                                {{ trans("Uploaded icon will displayed in the admin panel and will be for users") }}
                            </v-card-subtitle>

                            <v-divider/>

                            <div v-if="settings.favicon" class="d-flex align-end">
                                <v-img height="25px" class="mb-4" contain :src="settings.favicon" />
                            </div>

                            <v-file-input
                                v-model="faviconForm.file"
                                :label="trans('Select your icon')"
                                :placeholder="trans('Select your icon')"
                                class="mr-2 pt-4"
                                prepend-icon="mdi-paperclip"
                                filled
                                :show-size="1000"
                            >
                                <template v-slot:selection="{ index, text }">
                                <v-chip
                                    v-if="index < 2"
                                    color="primary"
                                    dark
                                    label
                                    small
                                >
                                    {{ text }}
                                </v-chip>
                                </template>
                            </v-file-input>

                            <v-btn type="submit" class="mx-2 mt-0 float-right" right color="primary"  :loading="faviconForm.processing">
                                <v-icon left>
                                    mdi-content-save
                                </v-icon>
                                {{trans("Save")}}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    name: "LogoUploader",
    inject: ['settings'],
    data(){
        return{
            logoForm: this.$inertia.form({
                file: null
            }),
            faviconForm: this.$inertia.form({
                file: null
            }),
        }
    },
    methods: {
        saveFile(form, route){
            form.post(route),{
                preserveScroll: true,
            }
        },
        saveLogo(){
            this.saveFile(this.logoForm, this.route('settings.uploadLogo'));
        },
        saveFavicon(){
            this.saveFile(this.faviconForm, this.route('settings.uploadFavicon'));
        }
    },
}
</script>