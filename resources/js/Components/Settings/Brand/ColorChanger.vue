<template>
    <v-card class="mt-4" outlined>
        <v-row class="ma-0 pa-0">
            <v-col cols="12">
                <v-card-title class="pt-1 ps-2 mb-0 ">
                    {{ trans('Theme color') }}
                </v-card-title>

                <v-card-subtitle class="ps-2 mb-0 pb-3">
                    {{ trans('The brand colors that appear on your store, social media, and more') }}
                </v-card-subtitle>

                <v-divider />

                <div class="d-flex ms-1 mt-4">
                    <div
                        @click="setTheme('#50bc00')"
                        style="background-color: #50bc00;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#ff4c51')"
                        style="background-color: #ff4c51;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#16b1ff')"
                        style="background-color: #16b1ff;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#6ABEA7')"
                        style="background-color: #6ABEA7;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#FFBC42')"
                        style="background-color: #FFBC42;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#ffd60a')"
                        style="background-color: #ffd60a;" class="color-option">
                    </div>
                    <div
                        @click="setTheme('#7678ed')"
                        style="background-color: #7678ed;" class="color-option">
                    </div>
                </div>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    inject: ['settings'],
    data(){
        return{
            form: this.$inertia.form({
                color: this.settings.color,
            }),
        }
    },
    methods: {
        setTheme(hex) {
            this.form.color = hex;

            this.form.put(this.route('settings.saveColor'),{
                preserveScroll: true,
                onSuccess: () => {
                    this.reloadPage()
                }
            })
        },
        reloadPage(){
            window.location.reload()
        }
    },
}
</script>


<style>
.color-option {
    width: 2.5rem!important;
    height: 2.5rem!important;
    margin-right: 5px;
    border-radius: 0.5rem;
    transition: all 0.25s ease 0s;
    cursor: pointer;
}
</style>
