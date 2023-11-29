<template>
<span>
    <span @click="dialog = true">
        <slot />
    </span>

    <v-dialog
        v-model="dialog"
        max-width="577px"
    >
        <v-card>
            <v-card-title class="dialog--title">
                {{ trans(title) }}

                <v-btn class="ms-auto" icon light @click="cancel">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text>
                <p class="dialog--content">{{ trans(content) }}</p>

                <slot name="header"></slot>
            </v-card-text>

            <v-card-actions class="px-5 pb-5 d-flex justify-space-between">
                <v-btn
                    class="text-none button"
                    outlined
                    color="secondary"
                    type="button"
                    @click="cancel">
                    {{ trans(cancelText) }}
                </v-btn>

                <v-btn
                    class="text-none button"
                    type="button"
                    :color="color"
                    @click="confirm"
                    :loading="loading">
                    {{ trans(button) }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</span>
</template>

<script>
/**
 * Vuetify Confirm Dialog component
 * https://gist.github.com/eolant/ba0f8a5c9135d1a146e1db575276177d
 *
 * Use with slots:
 * <confirms-modal @confirmed="action">
 *    //button
 * </confirms-modal>
 *
 * Or call it by ref:
 * <confirms-modal ref="confirm"></confirms-modal>
 * this.$refs.confirm.open().then((confirm) => {})
 * Or use await:
 * if (await this.$refs.confirm.open()) {
 *   // yes
 * }
 * else {
 *   // cancel
 * }
 */
export default {
    name: "ConfirmsModal",
    emits: ['confirmed'],
    props: {
        title: {
            default: 'Confirm action',
        },
        content: {
            default: 'Are you sure you want to continue?',
        },
        button: {
            default: 'Confirm',
        },
        color: {
            default: 'primary'
        },
        cancelText: {
            default: 'Cancel'
        }
    },
    data() {
        return {
            dialog: false,
            loading: false,
            resolve: function () {},
            reject: function () {},
        }
    },
    methods: {
        confirm() {
            this.loading = true

            setTimeout(() => {
                this.$emit('confirmed');      // for slot
                this.resolve(true);           // for ref
                this.loading = false
                this.dialog = false
            }, 300)
        },

        // used in ref
        open() {
            this.dialog = true;
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },

        cancel() {
            this.resolve(false);
            this.dialog = false
        },
    }
}
</script>
