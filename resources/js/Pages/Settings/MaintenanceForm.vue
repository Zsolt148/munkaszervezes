<template>
    <v-row>
        <v-col cols="12" md="7">
            <form-section @submitted="saveMaintenance">
                <template #title>
                    {{trans("Toggle maintenance")}}
                </template>

                <template #description>
                    {{ trans("Toggle maintenance mode, set up a password and add allowed IP addresses") }}
                </template>

                <template #form>
                    <v-row>
                        <v-col cols="12">
                            <v-switch
                                v-model="form.status"
                                inset
                                :label="trans('Toggle maintenance mode')"
                            ></v-switch>
                        </v-col>

                        <v-col cols="12">
                            <v-text-field
                                type="text"
                                filled
                                v-model="form.password"
                                persistent-hint
                                :error-messages="form.errors.password"
                                :hint="$page.props.url +'/1630542a-246b-4b66-afa1-dd72a4c43515'"
                                :label="trans('Password to access page while maintenance mode is active')"
                                :placeholder="trans('Password to access page while maintenance mode is active')"
                                hide-details="auto" />
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                type="text"
                                filled
                                v-model="temp_ip"
                                :label="trans('Allowed IP')"
                                :placeholder="trans('Add allowd IP-s')"
                                hide-details="auto"
                                @keydown.enter.prevent="addIp()">

                                <v-icon
                                    @click="addIp()"
                                    slot="append"
                                >mdi-plus
                                </v-icon>
                            </v-text-field>

                            <v-list class="px-0" v-if="form.ips && form.ips.length > 0">
                                <v-fade-transition
                                    class="py-0"
                                    hide-on-leave
                                    leave-absolute
                                    group
                                    tag="v-list"
                                >
                                    <template v-for="(ip, i) in form.ips">
                                        <v-divider
                                            v-if="i !== 0"
                                            :key="`${i}-divider`"
                                        ></v-divider>
                                        <v-list-item class="px-2" :key="`${i}-${ip}`">
                                            <v-list-item-subtitle> {{ ip }}</v-list-item-subtitle>
                                            <v-spacer></v-spacer>
                                            <v-list-item-action>
                                                <v-btn icon>
                                                    <v-icon @click="removeIp(i)" color="grey lighten-1">mdi-close-circle-outline</v-icon>
                                                </v-btn>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </template>
                                </v-fade-transition>
                            </v-list>
                        </v-col>
                    </v-row>
                </template>

                <template #actions>
                    <v-btn type="submit" class="mx-2 mt-0 float-right" right color="primary" >
                        <v-icon left>
                            mdi-content-save
                        </v-icon>
                        {{trans('Save changes')}}
                    </v-btn>
                </template>
            </form-section>
        </v-col>

        <v-col cols="12" md="5">
            <v-card class="mt-4" outlined>
                <v-row class="ma-0 pa-0">
                    <v-col cols="12">
                        <v-card-title class="pt-1 ps-2 mb-0 ">
                            {{ trans("Current status of website") }}
                        </v-card-title>

                        <v-card-subtitle class="ps-2 mb-0 pb-3">
                            {{ trans("In maintenance mode, visitors will not reach the website") }}
                        </v-card-subtitle>

                        <v-divider></v-divider>

                        <v-card-text class="text-center pt-5">
                            <div v-if="$page.props.page.maintenance" >
                                <v-icon color=primary x-large>
                                    mdi-alert
                                </v-icon>
                                <h3 class="pt-3">{{ trans("Under maintenance") }}</h3>
                            </div>
                            <div v-else >
                                <v-icon color=primary x-large>
                                    mdi-check-circle
                                </v-icon>
                                <h3 class="pt-3">{{ trans("Everything operates normally") }}</h3>
                            </div>
                        </v-card-text>
                    </v-col>
                </v-row>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import FormSection from '@/Components/Shared/FormSection.vue'
import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";

export default {
    components: {
        FormSection,
        ConfirmsPassword
    },
    inject: ['settings'],
    data() {
        return {
            temp_ip: null,
            form: this.$inertia.form({
                status: this.settings.is_maintenance,
                password: this.settings.maintenance_password,
                ips: this.settings.allowed_ips ?? []
            }),
        }
    },
    methods: {
        addIp() {
            if (this.temp_ip) {
                this.form.ips.push(this.temp_ip);
                this.temp_ip = '';
            }
        },
        removeIp(index) {
            this.form.ips.splice(index, 1);
        },
        saveMaintenance(){
            this.form.post(this.route('settings.saveMaintenanceMode'),{
                preserveScroll: true,
            })
        }
    },
}
</script>
