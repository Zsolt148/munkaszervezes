<template>
    <v-list-group ref="refVListGroup" class="vertical-nav-menu-group text--primary">
        <template #prependIcon>
        <v-icon>
            {{ icon }}
        </v-icon>
        </template>
        <template #activator>
        <v-list-item-title>
            {{ title }}
        </v-list-item-title>
        </template>

        <slot></slot>
    </v-list-group>
</template>

<script>
    export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
    },
    data() {
        return {}
    },
    }
</script>

<style lang="scss">
    .v-list-item {
        padding-left: 22px;
        margin-top: 0.375rem;
        height: 44px;
        min-height: 44px;

        .v-list-item__icon {
        margin-top: 0;
        margin-bottom: 0;
        align-self: center;
        }
    }

    .vertical-nav-menu-group {
        & > .v-list-group__header > .v-list-group__header__append-icon .v-icon {
            transform: rotate(-90deg);
        }

        &.v-list-group--active > .v-list-group__header > .v-list-group__header__append-icon .v-icon {
            transform: none;
        }
    }

    .v-application--is-ltr .v-list-item__action:first-child, .v-application--is-ltr .v-list-item__icon:first-child{
        margin-right: 12px!important;
    }
</style>
