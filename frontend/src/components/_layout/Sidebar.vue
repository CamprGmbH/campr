<template>
    <aside>
        <a href="/">
            <img class="logo" :src="logoUrl" alt="Logo"/>
        </a>
        <scrollbar class="sidebar customScrollbar">
            <nav>
                <ul>
                    <li class="active">
                        <router-link :to="{name: 'dashboard'}" title="Dashboard">
                            <span class="default">{{ translate('message.dashboard') }}</span>
                            <span class="tablet">
                                <svg version="1.1" class="dashboard-icon" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 23.6 23.6" xml:space="preserve">
                                <g id="XMLID_826_">
                                    <g>
                                        <rect id="XMLID_105_" x="2.8" y="2.4" class="st0" width="18" height="18.8"/>
                                        <line id="XMLID_104_" class="st0" x1="11.8" y1="13.9" x2="11.8" y2="8.2"/>
                                        <line id="XMLID_103_" class="st0" x1="16.7" y1="13.9" x2="16.7" y2="12.3"/>
                                        <line id="XMLID_102_" class="st0" x1="6.9" y1="9" x2="6.9" y2="4.9"/>
                                        <line id="XMLID_101_" class="st0" x1="6.9" y1="13.9" x2="6.9" y2="10.6"/>
                                        <line id="XMLID_100_" class="st0" x1="16.7" y1="10.6" x2="16.7" y2="4.9"/>
                                        <line id="XMLID_99_" class="st0" x1="11.8" y1="6.5" x2="11.8" y2="4.9"/>
                                        <rect id="XMLID_98_" x="15.1" y="10.6" class="st0" width="3.3" height="1.6"/>
                                        <rect id="XMLID_97_" x="10.2" y="6.5" class="st0" width="3.3" height="1.6"/>
                                        <rect id="XMLID_96_" x="5.3" y="9" class="st0" width="3.3" height="1.6"/>
                                        <circle id="XMLID_95_" class="st0" cx="16.8" cy="17.6" r="1.2"/>
                                        <circle id="XMLID_94_" class="st0" cx="11.8" cy="17.6" r="1.2"/>
                                        <circle id="XMLID_93_" class="st0" cx="6.9" cy="17.6" r="1.2"/>
                                    </g>
                                </g>
                                </svg>
                            </span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                                :to="{name: 'projects'}"
                                :title="translate('message.projects')">
                            <span class="default">{{ translate('message.projects') }}</span>
                            <span class="tablet">
                                <svg version="1.1" class="projects-icon" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 20.9 20.4" xml:space="preserve">
                                <g id="XMLID_823_">
                                    <path id="XMLID_825_" class="st0" d="M16.9,11.4h2.5V9h-2.5c-0.2-0.7-0.4-1.7-0.8-2.3l1.8-1.8l-2.2-2.2l-1.8,1.8
                                      c-0.6-0.4-1.6-0.6-2.3-0.8V1.2H9.3v2.5C8.6,3.9,7.6,4.2,7,4.5L5.2,2.7L3,4.9l1.8,1.8C4.4,7.3,4.1,8.3,4,9H1.4v2.3H4
                                      c0.2,0.7,0.4,1.7,0.8,2.3L3,15.5l2.2,2.2L7,15.9c0.6,0.4,1.6,0.6,2.3,0.8v2.5h2.3v-2.5c0.7-0.2,1.7-0.4,2.3-0.8l1.8,1.8l2.2-2.2
                                      l-1.8-1.8C16.5,13.1,16.7,12,16.9,11.4z"/>
                                    <circle id="XMLID_824_" class="st0" cx="10.4" cy="10.2" r="3.5"/>
                                </g>
                                  </svg>
                            </span>
                            <span class="notification-balloon"
                                  v-if="sidebarStats">{{ sidebarStats.projectTotal }}</span>
                        </router-link>
                    </li>
                    <li>
                        <router-link
                                :to="{name: 'tasks'}"
                                :title="translate('message.tasks')">
                            <span class="default">{{ translate('message.tasks') }}</span>
                            <span class="tablet">
                                <svg version="1.1" class="tasks-icon" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 20.2 19.8" xml:space="preserve">
                                <g id="XMLID_816_">
                                    <g id="XMLID_820_">
                                        <circle id="XMLID_822_" class="st0" cx="7.5" cy="12.6" r="2.4"/>
                                        <path id="XMLID_821_" class="st0" d="M12.5,11.5l1.2-0.7l-1.6-2.7l-1.2,0.7c-0.5-0.5-1.2-0.8-1.9-1.1V6.3H5.9v1.4
                                          C5.2,7.9,4.6,8.3,4.1,8.8L2.8,8.1l-1.6,2.7l1.2,0.7c-0.1,0.7-0.2,1.4,0,2.1l-1.2,0.7l1.6,2.7l1.2-0.7c0.5,0.5,1.2,0.8,1.9,1.1v1.4
                                          h3.2v-1.4c0.7-0.2,1.3-0.6,1.9-1.1l1.2,0.7l1.6-2.7l-1.2-0.7C12.7,13,12.7,12.2,12.5,11.5z"/>
                                    </g>
                                    <g id="XMLID_817_">
                                        <circle id="XMLID_819_" class="st0" cx="15.8" cy="4.3" r="1.2"/>
                                        <path id="XMLID_818_" class="st0" d="M18.5,3.7l0.8-0.5l-0.8-1.4l-0.8,0.5C17.4,2,17,1.8,16.6,1.7V0.8H15v0.9
                                          c-0.4,0.1-0.8,0.3-1.1,0.6l-0.8-0.5l-0.8,1.4l0.8,0.5c-0.1,0.4-0.1,0.9,0,1.3l-0.8,0.5l0.8,1.4l0.8-0.5c0.3,0.3,0.7,0.5,1.1,0.6
                                          v0.9h1.6V6.9c0.4-0.1,0.8-0.3,1.1-0.6l0.8,0.5l0.8-1.4l-0.8-0.5C18.6,4.5,18.6,4.1,18.5,3.7z"/>
                                    </g>
                                </g>
                                </svg>
                            </span>
                            <span class="notification-balloon" v-if="sidebarStats">{{ sidebarStats.taskTotal }}</span>
                        </router-link>
                    </li>
                </ul>
                <ul v-show="this.$route.name && this.$route.name.indexOf('project-') != -1">
                    <li class="separator"></li>
                    <li v-if="projectId">
                        <router-link :to="{name:'project-dashboard', params: {id: projectId}}"
                                     v-bind:title="translate('message.project_dashboard')">
                            <span class="default">{{ translate('message.project_dashboard') }}</span>
                            <span v-bind:class="'tablet'"></span>
                        </router-link>
                    </li>
                    <li v-for="(module, key) in modules" v-if="isProjectModuleActive(module)">
                        <router-link
                                v-if="projectId"
                                :to="{name: moduleToRoute[module], params: {id: projectId}}"
                                :title="translate(`modules.${module}.title`)">
                            <span class="default">{{ translate(`modules.${module}.title`) }}</span>
                            <span :class="module.icon"></span>
                        </router-link>
                    </li>
                    <li v-if="user.isAdmin" class="admin-link">
                        <a :href="adminRoute">Admin</a>
                    </li>
                    <li class="admin-link">
                        <a href="https://github.com/CamprGmbH/campr/issues" target="_blank">Feedback</a>
                    </li>
                    <li class="admin-link">
                        <a href="https://www.youtube.com/channel/UCgP6VLB0CJdRznCFBiRh5BQ/playlists?view_as=subscriber"
                           target="_blank">
                            Tutorial
                        </a>
                    </li>
                </ul>
            </nav>
        </scrollbar>
    </aside>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';

    export default {
        name: 'sidebar',
        props: ['user'],
        created() {
            this.getSidebarInformation();
            if (!this.modules || !this.modules.length) {
                this.getModules();
            }
        },
        watch: {
            projectId(val) {
                if (val !== undefined) {
                    this.getProjectById(val);
                } else {
                    this.clearProject();
                }
            },
        },
        beforeUpdate() {
            this.projectId = this.$route.params.id;
        },
        computed: {
            ...mapGetters([
                'modules',
                'sidebarStats',
                'isProjectModuleActive',
                'logoUrl',
            ]),
        },
        methods: {
            ...mapActions([
                'getModules',
                'getProjectById',
                'clearProject',
                'getSidebarInformation',
            ]),
        },
        data() {
            return {
                projectId: null,
                moduleToRoute: {
                    contract: 'project-contract',
                    organization: 'project-organization',
                    phases_and_milestones: 'project-phases-and-milestones',
                    task_management: 'project-task-management-list',
                    external_costs: 'project-external-costs',
                    internal_costs: 'project-internal-costs',
                    risks_and_opportunities: 'project-risks-and-opportunities',
                    gantt_chart: 'project-gantt-chart',
                    rasci_matrix: 'project-rasci-matrix',
                    wbs: 'project-wbs',
                    meetings: 'project-meetings',
                    todos: 'project-todos',
                    infos: 'project-infos',
                    decisions: 'project-decisions',
                    status_report: 'project-status-reports',
                    close_down_project: 'project-close-down-report',
                },
                adminRoute: Routing.generate('app_admin_dashboard'),
            };
        },
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
    @import '~theme/variables';
    @import '../../css/_mixins';

    .sidebar {
        width: 200px;
        height: 90vh;
        padding-right: 10px;
    }

    ul {
        list-style-type: none;
        padding: 0;
        text-align: left;
    }

    .separator {
        height: 1px;
        background: $mainColor;
        margin: 14px 5px;
        padding: 0;
    }

    .st0 {
        fill: none;
        stroke: #666FA1;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-miterlimit: 10;
    }

    .dashboard-icon {
        enable-background: new 0 0 23.6 23.6;
    }

    .projects-icon {
        enable-background: new 0 0 20.9 20.4;
    }

    .tasks-icon {
        enable-background: new 0 0 20.2 19.8;
    }

    aside {
        width: 210px;
        padding: 10px 0 10px 10px;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        background: $darkColor;

        @media screen and (max-width: 768px) {
            width: 59px;

            .default {
                display: none;
            }

            .tablet {
                display: block;
            }
        }

        .logo {
            margin: 14px 26px;
            display: block;
            width: 142px;

            @media screen and (max-width: 768px) {
                width: 39px;
                margin: 14px 0;
            }
        }

        nav {
            color: $lightColor;
            font-size: 11px;
            text-transform: uppercase;
            bottom: 0px;

            ul {
                margin-left: 0px;

                li {
                    padding: 2px 0;
                    position: relative;

                    &.admin-link {
                        a {
                            background: darken($secondColor, 15%);
                            color: $whiteColor;
                            @include transition(all, 0.2s, ease);
                            text-align: center;

                            &:hover {
                                background: darken($secondColor, 25%);
                                color: $whiteColor;
                            }
                        }
                    }

                    .notification-balloon {
                        right: 5px;
                        top: 9px;

                        @media screen and (max-width: 768px) {
                            top: -1px;
                            right: -9px;
                        }
                    }

                    a {
                        display: block;
                        padding: 8px 10px;
                        border-radius: 15px;
                        letter-spacing: 0.1em;

                        &.router-link-active, &:hover {
                            color: $lighterColor;
                            background: $mainColor;

                            .st0 {
                                stroke: $lighterColor;
                            }
                        }
                    }
                }
            }
        }
    }
</style>
