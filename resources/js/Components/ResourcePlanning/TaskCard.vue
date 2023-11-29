<template>
    <div>
        <v-menu z-index="15" transition="scale-transition" v-model="menu" offset-x right v-if="isTask">
            <template v-slot:activator="{ on }">
                <v-card outlined ripple class="rp--card handle mx-auto list-group-item mb-2">
                    <v-card-text class="ma-0 pa-0" v-on="on">
                        <div class="rp--card-data">
                            <span class="rp--card-id">#{{ task.id }} - {{ task.park.name }}
                                <v-icon v-if="task.role_id" class="ms-auto" @click="removeTask()">
                                    mdi-delete
                                </v-icon>
                            </span>

                            <div class="rp--card-name">
                                {{ `${task.name.substring(0, 32)}...` }}
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </template>

            <canban-card :task="task"/>
        </v-menu>

        <v-menu :close-on-content-click="false" transition="scale-transition" v-else z-index="15" v-model="menu" bottom
                offset-x right>
            <template v-slot:activator="{ on }">
                <v-card v-on="on" outlined ripple class="rp--card-leave mx-auto list-group-item mb-2">
                    <v-card-text class="ma-0 pa-0">
                        <div class="rp--card-data">
                            <div class="rp--card-leave-name">
                                {{ task.type_text }}
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </template>

            <v-list>
                <v-list-item>
                    <v-btn @click="declineLeaveDay(task)" text>
                        <v-icon class="me-2">mdi-cancel</v-icon>
                        {{ trans('Decline leave day / sick leave') }}
                    </v-btn>
                </v-list-item>
            </v-list>
        </v-menu>

        <confirms-modal ref="confirm"/>
    </div>
</template>

<script>
import {flash} from "@/Use/helpers"
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";
import CanbanCard from "@/Components/ResourcePlanning/CanbanCard";

export default {
    name: "TaskCard",
    props: ['task'],
    data() {
        return {
            menu: false
        }
    },
    components: {
        CanbanCard,
        ConfirmsModal
    },
    methods: {
        async removeTask() {
            const resp = await axios.patch(this.route('resource-planning.remove', {task: this.task.id}));
            this.$emit('fetchData');
            flash(this, resp.data)
        },

        async declineLeaveDay(item) {
            if (await this.$refs.confirm.open()) {
                const resp = await axios.patch(this.route("tasks.leave.decline", item.id));
                this.$emit('fetchData');
                flash(this, resp.data)
            }
        }
    },
    computed: {
        isTask() {
            return this.task.name;
        }
    }
}
</script>