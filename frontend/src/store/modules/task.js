import Vue from 'vue';
import * as types from '../mutation-types';

const state = {
    currentTask: {},
    taskHistory: [],
    tasks: [],
    tasksCount: 0,
    filteredTasks: [],
    taskFilters: [],
    users: [],
    tasksFilters: {},
    allTasks: [],
    tasksPageSize: 0,
};

const getters = {
    currentTask: state => state.currentTask,
    tasks: state => state.tasks,
    tasksCount: state => state.tasksCount,
    tasksPerPage: state => state.tasksPageSize,
    allTasks: state => state.allTasks,
    taskHistory: state => state.taskHistory,
};

const actions = {
    /**
     * Gets this month tasks from the API and commits SET_TASKS mutation
     * @param {function} commit
     * @param {array} data
     */
    getRecentTasks({commit}, data) {
        let paramObject = {params: {
            recent: true,
            type: 2,
        }};
        if (data && data.queryParams && data.queryParams.page !== undefined) {
            paramObject.params.page = data.queryParams.page;
        }
        if (state.taskFilters && state.taskFilters.status) {
            paramObject.params.status = state.taskFilters.status;
        }
        if (state.taskFilters && state.taskFilters.project) {
            paramObject.params.project = state.taskFilters.project;
        }
        Vue.http
            .get(Routing.generate('app_api_workpackage_list'), paramObject)
            .then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_TASKS, {tasks});
                }
            }, (response) => {
            });
    },
    getRecentTasksByProject({commit}, projectId) {
        commit(types.SET_TASKS, {tasks: []});
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
                } else {
                    commit(types.SET_TASKS, {tasks: []});
                }
            }, (response) => {
                commit(types.SET_TASKS, {tasks: []});
            });
    },
    /**
     * Gets tasks from the API and commits SET_TASKS mutation
     * @param {function} commit
     * @param {array} data
     */
    getTasks({commit}, data) {
        let paramObject = {params: {
            type: 2,
        }};
        if (data && data.queryParams && data.queryParams.page !== undefined) {
            paramObject.params.page = data.queryParams.page;
        }
        if (state.taskFilters && state.taskFilters.status) {
            paramObject.params.status = state.taskFilters.status;
        }
        if (state.taskFilters && state.taskFilters.project) {
            paramObject.params.project = state.taskFilters.project;
        }
        Vue.http
            .get(Routing.generate('app_api_workpackage_list'), paramObject)
            .then((response) => {
                if (response.status === 200) {
                    let tasks = response.data;
                    commit(types.SET_TASKS, {tasks});
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
    getTaskHistory({commit}, id) {
        Vue.http
            .get(Routing.generate('app_api_workpackage_history', {'id': id})).then((response) => {
                if (response.status === 200) {
                    let history = response.data;
                    commit(types.SET_TASK_HISTORY, {history});
                }
            }, (response) => {
            });
    },
    /**
     * Creates a new task on project
     * @param {function} commit
     * @param {array} data
     * @param {Number} projectId
     * @return {object}
     */
    createNewTask({commit}, data) {
        return Vue
            .http
            .post(
                Routing.generate('app_api_project_tasks_create', {'id': data.projectId}),
                data.data
            )
            .then(
                (response) => {
                    if (response.body && response.body.error) {
                        const {messages} = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages});
                    } else {
                        const task = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages: []});
                        commit(types.SET_TASK, {task});
                    }
                },
                (response) => {}
            )
        ;
    },

    /**
     * Edit an existing task
     * @param {function} commit
     * @param {array} data
     * @return {Object}
     */
    editTask({commit}, data) {
        return Vue.http
            .post(
                Routing.generate('app_api_workpackage_edit', {'id': data.taskId}),
                data.data
            ).then(
                (response) => {
                    if (response.body && response.body.error && response.body.messages) {
                        const {messages} = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages});
                    } else {
                        const task = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages: []});
                        commit(types.SET_TASK, {task});
                    }
                },
                (response) => {}
            );
    },
    /**
     * Patch an existing task
     * @param {function} commit
     * @param {array} data
     * @return {Object}
     * @todo change this to a more suitable version
     */
    patchTask({commit}, data) {
        return Vue.http
            .patch(
                Routing.generate('app_api_workpackage_edit', {'id': data.taskId}),
                data.data
            ).then(
                (response) => {
                    if (response.body && response.body.error) {
                        const {messages} = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages});
                    } else {
                        const task = response.body;
                        commit(types.SET_VALIDATION_MESSAGES, {messages: []});
                        commit(types.SET_TASK, {task});
                    }
                },
                (response) => {}
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
        commit(types.SET_TASKS_FILTERS, {filters});
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
    /**
     * Delete subtask
     * @param {function} commit
     * @param {integer} taskId
     */
    deleteTaskSubtask({commit}, taskId) {
        Vue.http
            .delete(
                Routing.generate('app_api_workpackage_delete', {id: taskId})
            ).then((response) => {
                if (response.body && response.body.error) {
                    const {messages} = response.body;
                    commit(types.SET_VALIDATION_MESSAGES, {messages});
                } else {
                    commit(types.SET_VALIDATION_MESSAGES, {messages: []});
                    commit(types.DELETE_TASK_SUBTASK, taskId);
                }
            }, (response) => {
                // implement alert response error here
            });
    },
    /**
     * Add new comment
     * @param {function} commit
     * @param {array} data
     */
    addTaskComment({commit}, data) {
        Vue.http
            .post(Routing.generate('app_api_workpackage_comments_create', {'id': data.task.id}), JSON.stringify(data.payload)).then((response) => {
                if (response.status === 200) {
                    Vue.http
                        .get(Routing.generate('app_api_workpackage_history', {'id': data.task.id})).then((response) => {
                            if (response.status === 200) {
                                let history = response.data;
                                commit(types.SET_TASK_HISTORY, {history});
                            }
                        }, (response) => {
                        });
                }
            }, (response) => {
            });
    },
};

const mutations = {
    mutations,
    /**
     * Sets tasks to state
     * @param {Object} state
     * @param {array} tasks
     */
    [types.SET_TASKS](state, {tasks}) {
        state.tasks = tasks.items;
        state.tasksCount = tasks.totalItems;
        state.tasksPageSize = tasks.pageSize;
        state.filteredTasks = JSON.parse(JSON.stringify(tasks));
    },
    /**
     * Sets task to state
     * @param {Object} state
     * @param {Object} task
     */
    [types.SET_TASK](state, {task}) {
        state.currentTask = task;
    },
    /**
     * Set the history
     * @param {Object} state
     * @param {Object} history
     */
    [types.SET_TASK_HISTORY](state, {history}) {
        state.taskHistory = history;
    },
    /**
     * Set the tasks filters
     * @param {Object} state
     * @param {array} filters
     */
    [types.SET_TASKS_FILTERS](state, {filters}) {
        state.taskFilters = !filters.clear ? Object.assign({}, state.taskFilters, filters) : [];
    },

    /**
     * Set all tasks
     * @param {Object} state
     * @param {array} tasks
     */
    [types.SET_ALL_TASKS](state, {tasks}) {
        state.allTasks = tasks;
    },

    /**
     * Delete a subtask
     * @param {Object} state
     * @param {number} taskId
     */
    [types.DELETE_TASK_SUBTASK](state, taskId) {
        state.currentTask.children = state.currentTask.children.filter((item) => {
            return item.id !== taskId ? true : false;
        });

        let decrementNeeded = false;
        state.tasks = state.tasks.filter((item) => {
            if (item.id === taskId) {
                decrementNeeded = true;
                return false;
            }
            return true;
        });

        if (decrementNeeded) {
            state.tasksCount--;
        }
    },
    /**
     * Add new cost for task
     * @param {Object} state
     * @param {Object} cost
     */
    [types.ADD_TASK_COST](state, cost) {
        state.currentTask.costs.push(cost);
    },
    /**
     * Removes a cost of a task
     * @param {Object} state
     * @param {Object} costId
     */
    [types.REMOVE_TASK_COST](state, costId) {
        state.currentTask.costs = state.currentTask.costs.filter((item) => {
            return item.id !== costId ? true : false;
        });
    },
    /**
     * Updates cost for task
     * @param {Object} state
     * @param {Object} cost
     */
    [types.SET_TASK_COST](state, cost) {
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
