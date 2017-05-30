import Vue from 'vue';
import * as types from '../mutation-types';
import router from '../../router';

const state = {
    currentItem: {},
    items: [],
    totalItems: 0,
    filteredItems: [],
    filters: [],
    users: [],
    tasksFilters: [],
    allTasks: [],
};

const getters = {
    currentTask: state => state.currentItem,
    tasks: state => state.items,
    tasksCount: state => state.totalItems,
    allTasks: state => state.allTasks,
};

const actions = {
    /**
     * Gets this month tasks from the API and commits SET_TASKS mutation
     * @param {function} commit
     * @param {number} page
     */
    getRecentTasks({commit}, page) {
        commit(types.TOGGLE_LOADER, true);
        if (page === undefined) {
            page = 1;
        }
        Vue.http
            .get(Routing.generate('app_api_workpackage_list', {recent: true, page: page, type: 2})).then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_TASKS, {tasks});
                    commit(types.TOGGLE_LOADER, false);
                }
            }, (response) => {
            });
    },
    getRecentTasksByProject({commit}, projectId) {
        commit(types.TOGGLE_LOADER, true);
        let params = {
            id: projectId,
            sort: 'updatedAt',
            order: 'desc',
            limit: 6,
        };
        Vue.http
            .get(Routing.generate('app_api_project_tasks', params)).then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_TASKS, {tasks});
                    commit(types.TOGGLE_LOADER, false);
                }
            }, (response) => {
            });
    },
    /**
     * Gets tasks from the API and commits SET_TASKS mutation
     * @param {function} commit
     * @param {number} page
     */
    getTasks({commit}, page) {
        commit(types.TOGGLE_LOADER, true);
        Vue.http
            .get(Routing.generate('app_api_workpackage_list', {page: page, type: 2})).then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_TASKS, {tasks});
                    commit(types.TOGGLE_LOADER, false);
                }
            }, (response) => {
            });
    },
    /**
     * Gets a task by ID from the API and commits SET_TASK mutation
     * @param {function} commit
     * @param {number} id
     */
    getTaskById({commit}, id) {
        Vue.http
            .get(Routing.generate('app_api_workpackage_get', {'id': id})).then((response) => {
                if (response.status === 200) {
                    let task = response.data;
                    commit(types.SET_TASK, {task});
                }
            }, (response) => {
            });
    },
    /**
     * Creates a new task on project
     * @param {function} commit
     * @param {array} data
     * @param {Number} projectId
     */
    createNewTask({commit}, data) {
        Vue.http
            .post(
                Routing.generate('app_api_project_tasks_create', {'id': data.projectId}),
                data.data
            ).then((response) => {
                router.push({name: 'project-task-management-list'});
                // @TODO: use this redirect when we figure out why this is getting broken
                // router.push({
                //     name: 'project-task-management-edit',
                //     params: {
                //         id: data.projectId,
                //         taskId: response.data.id,
                //     },
                // });
            }, (response) => {
                if (response.status === 400) {
                    // implement system to display errors
                }
            });
    },

    /**
     * Edit an existing task
     * @param {function} commit
     * @param {array} data
     */
    editTask({commit}, data) {
        Vue.http
            .post(
                Routing.generate('app_api_workpackage_edit', {'id': data.taskId}),
                data.data
            ).then(
                (response) => {
                    // router.push({name: 'project-task-management-list'});
                    if (response.status === 200) {
                        alert('Saved!');
                    } else {
                        alert(response.status);
                    }
                },
                (response) => {
                    alert('Validation issues');
                    if (response.status === 400) {
                        // implement system to dispay errors
                    }
                }
            );
    },

    getUsers({commit}) {
        Vue.http
            .post(
                Routing.generate('app_api_workpackage_edit', {'id': data.taskId}),
                data.data
            ).then(
            (response) => {
                if (response.status === 200) {
                    alert('Saved!');
                } else {
                    alert(response.status);
                }
            },
            (response) => {
                alert('Validation issues');
                if (response.status === 400) {
                    // implement system to dispay errors
                }
            }
        );
    },

    setFilters({commit}, filters) {
        commit(types.SET_TASKS_FILTERS, filters);
    },
    getAllTasksGrid({commit, state}, {project, page}) {
        const projectUser = state.tasksFilters.assignee;
        const colorStatus = state.tasksFilters.condition;
        const searchString = state.tasksFilters.searchString;
        const status = state.tasksFilters.status;

        Vue.http
            .get(Routing.generate('app_api_projects_workpackages', {'id': project}), {
                params: {
                    'type': 2,
                    'page': page,
                    'pageSize': 8,
                    projectUser,
                    colorStatus,
                    searchString,
                    'isGrid': true,
                    status,
                },
            })
            .then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_ALL_TASKS, tasks);
                    // commit(types.TOGGLE_LOADER, false);
                }
            }, (response) => {
            });
    },
};

const mutations = {
    /**
     * Sets color statuses to state
     * @param {Object} state
     * @param {array} colorStatuses
     */
    [types.SET_COLOR_STATUSES](state, {colorStatuses}) {
        state.items = colorStatuses;
        state.filteredItems = colorStatuses;
    },
    /**
     * Sets tasks to state
     * @param {Object} state
     * @param {array} tasks
     */
    [types.SET_TASKS](state, {tasks}) {
        // state.items = {
        //     items: tasks.items,
        //     totalNumber: tasks.totalNumber,
        // };
        state.items = tasks.items;
        state.totalItems = tasks.totalItems;
        state.filteredItems = JSON.parse(JSON.stringify(tasks));
    },
    /**
     * Sets task to state
     * @param {Object} state
     * @param {Object} task
     */
    [types.SET_TASK](state, {task}) {
        state.currentItem = task;
    },
    /**
     * Sets filters to state
     * @param {Object} state
     * @param {array} filter
     */
    [types.SET_FILTERS](state, filter) {
        state.filters[filter[0]] = filter[1];
    },
    [types.SET_TASKS_FILTERS](state, filters) {
        state.tasksFilters = filters;
    },
    [types.SET_ALL_TASKS](state, tasks) {
        state.allTasks = tasks;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
