<template>
    <v-dialog hide-overlay transition="dialog-bottom-transition">
        <template v-slot:activator="{ on, attrs }">
            <v-btn color="primary" dark v-bind="attrs" v-on="on">
                Open Dialog
            </v-btn>
        </template>
        <v-card>
            <v-toolbar dark color="primary">
                <v-btn icon dark @click="dialog = false">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-toolbar-title>Settings</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                    <v-btn dark text @click="dialog = false">
                        Save
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-list three-line subheader>
                <v-subheader>User Controls</v-subheader>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Content filtering</v-list-item-title>
                        <v-list-item-subtitle>Set the content filtering level to restrict apps that can be
                            downloaded</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Password</v-list-item-title>
                        <v-list-item-subtitle>Require password for purchase or use password to restrict
                            purchase</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-card>
    </v-dialog>
</template>

<script>
export default{
    name: 'LogoutModal',
    methods:{
        logout(){
            this.$emit('confirmed');
        }
    }
}
</script>