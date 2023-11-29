<template>
    <div>
        <v-navigation-drawer
            :mini-variant="Boolean(mini)"
            :style="navigationStyle"
            v-model="isDrawerOpen"
            app
            class="app-navigation-menu"
        >
            <div class="vertical-nav-header d-flex pa-5">
                <nav-logo class="pb-0 m-0 m-auto" v-if="!mini" />

                <v-app-bar-nav-icon
                    v-if="$vuetify.breakpoint.smAndDown"
                    class="ms-auto"
                    @click="isDrawerOpen = !isDrawerOpen">
                </v-app-bar-nav-icon>

                <v-icon v-else-if="mini" x-large color="primary" class="ml-n3" @click.stop="mini ? maximise() : minify()">mdi-cog-clockwise</v-icon>
                <v-btn
                    v-if="!$vuetify.breakpoint.smAndDown && !mini"
                    class="mt-3 ml-auto"
                    icon
                    @click.stop="mini ? maximise() : minify()">
                    <v-icon v-if="mini">mdi-chevron-right</v-icon>
                    <v-icon v-else>mdi-chevron-left</v-icon>
                </v-btn>
            </div>

            <v-list expand class="vertical-nav-menu-items pr-3" v-if="$vuetify.breakpoint.mdAndDown">
                <v-list-item class="mb-5">
                    <app-search/>
                </v-list-item>
            </v-list>

            <v-list>
                <component
                    :is="item.children ? 'v-list-group' : 'v-list-item'"
                    :class="{'primary': isUrl(item.url)}"
                    :value="active(item)"
                    v-for="item in items"
                    v-show="item.show"
                    :key="item.title"
                    :id="item.id"
                    @click="item.route ? $inertia.visit(item.route) : null"
                    link
                >
                    <template v-slot:activator v-if="item.children">
                        <v-list-item-icon>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title class="navbar--link">{{ trans(item.title) }}</v-list-item-title>
                        </v-list-item-content>
                    </template>

                    <v-list-item
                        v-if="item.children"
                        v-for="child in item.children"
                        v-show="child.show"
                        :class="{'primary': isUrl(child.url)}"
                        @click="child.route ? $inertia.visit(child.route) : null"
                        :key="child.title"
                        :id="child.id"
                        link
                    >
                        <v-list-item-icon class="ps-3">
                            <v-icon>{{ child.icon }}</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title class="navbar--link">{{ trans(child.title) }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item-icon v-if="!item.children">
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content v-if="!item.children">
                        <v-list-item-title class="navbar--link">{{ trans(item.title) }}</v-list-item-title>
                    </v-list-item-content>
                </component>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            app
            flat
            sticky
            :style="{background: $vuetify.theme.dark ? $vuetify.theme.themes.dark.appbar : $vuetify.theme.themes.light.appbar}">
            <div class="boxed-container w-full">
                <div class="d-flex align-center mx-md-6">
                    <v-app-bar-nav-icon class="d-block d-lg-none me-2"
                                        @click="isDrawerOpen = !isDrawerOpen"></v-app-bar-nav-icon>
                    <app-search v-if="!$vuetify.breakpoint.mdAndDown"/>
                    <v-spacer></v-spacer>
                    <v-fade-transition mode="out-in" v-if="$page.props.hasAdminProxy">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-icon @click="$inertia.visit(route('admin-proxy.exit'))"
                                        class="mx-2"
                                        v-bind="attrs"
                                        v-on="on">
                                    mdi-undo
                                </v-icon>
                            </template>
                            <span>Visszalépés a felhasználómba</span>
                        </v-tooltip>
                    </v-fade-transition>

                    <v-fade-transition  v-if="!$vuetify.breakpoint.mdAndDown" mode="out-in">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-icon @click="startTour()"
                                        v-bind="attrs"
                                        v-on="on"
                                        class="mx-2">
                                    mdi-flag-outline
                                </v-icon>
                            </template>
                            <span>Bemutató megkezdése</span>
                        </v-tooltip>
                    </v-fade-transition>

                    <notifications/>

                    <user-menu/>
                </div>
            </div>
        </v-app-bar>

        <v-tour name="myTour" :steps="steps" :options="tourOptions"></v-tour>

    </div>
</template>
<script>
import UserMenu from '@/Components/Shared/Nav/UserMenu.vue'
import AppSearch from "@/Components/Shared/Nav/AppSearch.vue";
import Notifications from '@/Components/Shared/Nav/Notifications.vue';
import NavLogo from "@/Components/Shared/Nav/NavLogo.vue";

export default {
    name: 'NavbarInner',
    components: {
        AppSearch,
        Notifications,
        UserMenu,
        NavLogo
    },
    data() {
        return {
            isDrawerOpen: true,
            drawer: this.$vuetify.breakpoint.mdAndDown ? false : true,
            mini: localStorage.getItem('mini') == '1',
            lang: this.$page.props.auth.user.locale ?? 'hu',
            items: [
                {
                    id: 'v-step-0',
                    title: 'Notifications',
                    icon: 'mdi-bell',
                    route: this.route('notifications.index'),
                    url: '/notifications',
                    show: true
                },
                {
                    id: 'v-step-1',
                    title: 'Users',
                    icon: 'mdi-account-multiple',
                    route: this.route('admins.index'),
                    url: '/admins',
                    show: this.hasPermission('view_admins')
                },
                {
                    id: 'v-step-2',
                    title: 'Task manager',
                    icon: 'mdi-checkbox-multiple-marked',
                    route: this.route('tasks.index', {tab: 'all'}),
                    url: '/tasks',
                    show: this.hasPermission('tasks')
                },
                {
                    id: 'v-step-3',
                    title: 'Resource planning',
                    icon: 'mdi-calendar-today',
                    route: this.route('resource-planning.index', {tab: 'group'}),
                    url: '/resource-planning',
                    show: this.hasPermission('resource_planning')
                },
                {
                    id: 'v-step-4',
                    title: 'Admin',
                    icon: 'mdi-shield-crown-outline',
                    show: (
                        this.hasPermission('view_logs') ||
                        this.hasPermission('update_settings')
                    ),
                    children: [
                        {
                            title: 'Logs',
                            icon: 'mdi-note-multiple',
                            route: this.route('logs.index'),
                            url: '/logs',
                            show: this.hasPermission('view_logs')
                        },
                        {
                            title: 'Settings',
                            icon: 'mdi-cog',
                            route: this.route('settings.index'),
                            url: '/settings',
                            show: this.hasPermission('update_settings')
                        },
                    ]
                },
            ],
            tourOptions: {
                highlight: true,
                labels: {
                    buttonSkip: this.trans("Skip"),
                    buttonPrevious: this.trans("Previous"),
                    buttonNext: this.trans("Next"),
                    buttonStop: this.trans("Finish")
                }
            },
            steps: [
                {
                    target: '#v-step-0',
                    header: {
                        title: 'Értesítések',
                    },
                    content: 'Értesítések',
                    params: {
                        placement: 'right'
                    }
                },
                {
                    target: '#v-step-1',
                    header: {
                        title: 'Felhasználók',
                    },
                    content: 'Felhasználók/adminisztrátorok meghívása, szerkesztése, törlése és feloldása',
                    params: {
                        placement: 'right'
                    }
                },
                {
                    target: '#v-step-2',
                    header: {
                        title: 'Feladatkezelő',
                    },
                    content: 'Feladatok (kanban board), elvégzett feladatok, szabadság és táppánz kezelése',
                    params: {
                        placement: 'right'
                    }
                },
                {
                    target: '#v-step-3',
                    header: {
                        title: 'Munkaszervezés',
                    },
                    content: 'Osztályok közti feladatkiosztás, beosztottak közti feladatkiosztás és előtervezés.',
                    params: {
                        placement: 'right'
                    }
                },
            ],
        }
    },
    created() {
        if (this.$vuetify.breakpoint.mdAndDown) {
            this.isDrawerOpen = false;
        }
    },
    methods: {
        minify() {
            this.mini = true;
            localStorage.setItem('mini', "1");
        },
        maximise() {
            this.mini = false;
            localStorage.setItem('mini', "0");
        },
        toggleMini() {
            this.mini = false; // set mini to false
            localStorage.setItem('mini', '0');
        },
        startTour() {
            this.$tours['myTour'].start()
        },
        changeLanguage() {
            this.$inertia.visit(this.route('admin:language', this.lang))
        },
        goToWebsite() {
            window.open(this.route('home'), '_blank')
        },
        active(item) {
            if (item.children) {
                return item.children.filter((child) => this.isUrl(child.url)).length
            }

            return false
        }
    },
    computed: {
        navigationStyle() {
            if (this.$vuetify.breakpoint.smAndDown) return {width: '100%'}
            else return
        }
    }
};

</script>
<style lang="scss" scoped>
.v-app-bar {
    box-shadow: none !important;
    border-bottom: thin solid rgba(0, 0, 0, .12);
    margin: auto;
    border-radius: 5px;
    backdrop-filter: blur(8px);
}

.v-navigation-drawer--mini-variant {
    .v-list-group {
        .v-list-item--link {
            padding-left: 16px !important;
        }
    }

    .theme--light.v-list-item:not(.v-list-item--active):not(.v-list-item--disabled) {
        padding-left: 0px !important;
    }

    .v-list-group {
        padding-left: 0px !important;

        .v-list-item {
            padding-left: 0px !important;
        }
    }

    .v-list-item {
        padding-left: 0px !important;
    }

    .v-list-group--active {
        .v-list-item {
            padding-left: 0px !important;
            padding-right: 0px !important;
            display: flex;
            justify-content: center;
            align-items: center;

            .v-list-item__icon {
                margin-right: 0px !important;
            }
        }
    }

    .v-list-group {
        .v-list-item {
            padding-left: 0px !important;
            padding-right: 0px !important;
            display: flex;
            justify-content: center;
            align-items: center;

            .v-list-item__icon {
                margin-right: 0px !important;
            }
        }
    }

    .v-list-item {
        padding-left: 0px !important;
        padding-right: 0px !important;
        display: flex;
        justify-content: center;
        align-items: center;

        .v-list-item__icon {
            margin-right: 0px !important;
        }
    }

}

.primary {
    i {
        color: white;
    }

    color: white !important;
}</style>
