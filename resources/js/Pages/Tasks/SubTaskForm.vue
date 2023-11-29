<template>
    <v-row>
        <v-col cols="12">
            <v-autocomplete
                v-model="form.subtasks[index]"
                :loading="isLoading"
                :items="filteredItems(index)"
                :search-input.sync="search"
                :label="trans('Subtask') + ' #' + (index+1)"
                :placeholder="trans('Start typing to search')"
                item-text="name"
                item-value="id"
                return-object
                dense
                filled
                hide-no-data
                hide-details
            >
                <template v-slot:selection="{ attr, on, item, selected }">
                    <b>{{ item.name }}</b>
                    <span class="ps-1">- {{ item.statusText }} - {{ item.deadline }}</span>
                </template>
                
                <template v-slot:item="{ item }">
                    <v-list-item-avatar v-if="item.responsible">
                        <v-img :src="getProfPicture(item.responsible.profile_photo_url, item.responsible.name)"></v-img>
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title>{{ item.name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ item.statusText }} - {{ item.deadline }}</v-list-item-subtitle>
                    </v-list-item-content>
                </template>
            </v-autocomplete>


            <v-row class="pa-3" v-if="form.subtasks[index]">
                <v-col class="story--card-data">
                    {{ form.subtasks[index].name }}
                </v-col>

                <v-col class="story--card-data">
                    <p class="responsible">{{ form.subtasks[index].responsible ? form.subtasks[index].responsible.name : '-' }}</p>
                    <p class="role">{{ form.subtasks[index].roleText }}</p>
                </v-col>

                <v-col class="story--card-data">
                    <p class="date">{{ form.subtasks[index].date }}</p>
                    <p class="hour">{{ form.subtasks[index].estimated_hour }} {{ trans('hour') }}</p>
                </v-col>

                <v-col class="story--card-button">
                    <v-btn color="primary" @click="removeTask" outlined>
                        <v-icon color="primary">mdi-delete-outline</v-icon>
                    </v-btn>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: "SubTaskForm",
    props: ['index', 'form'],
    data() {
        return {
            isLoading: false,
            items: this.form.subtasks ?? [],
            search: null,
            timeout: null,
        }
    },
    methods: {
        removeTask() {
            this.form.subtasks.splice(this.index, 1)
        },
        filter(index, items) {
            // if current v model is set
            if (this.form.subtasks[index]) {
                //
                let selected = [];
                selected.push(this.form.subtasks[index])

                // leave only selected in the items array
                const results = items.filter(x => x).filter(({ id: id1 }) => selected.some(({ id: id2 }) => id2 === id1));

                return results
            }

            // current v-model is empty
            let subtasks = this.form.subtasks.filter(x => x)

            // filter out other selected items
            const results = items.filter(x => x).filter(({ id: id1 }) => !subtasks.some(({ id: id2 }) => id2 === id1));

            return results
        }
    },
    watch: {
        search (val) {
            // search input is empty
            if (!val) return

            // Items have already been requested
            if (this.isLoading) return

            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {
                this.isLoading = true

                // Lazily load input items
                axios.get(this.route('tasks.search.subtasks', {search: val}))
                    .then(res => {
                        this.items = res.data
                    })
                    .catch(err => {
                        console.error(err)
                    })
                    .finally(() => (this.isLoading = false))
            }, 200)
        },
    },
    computed: {
        filteredItems() {
            return index => this.filter(index, this.items);
        }
    }
}
</script>