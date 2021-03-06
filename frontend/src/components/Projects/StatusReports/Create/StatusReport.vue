<template>
    <div>
        <div class="header">
            <h1>
                {{ projectName }}
                <span>{{ translate('message.week') }} {{ weekNumber }}</span>
            </h1>
        </div>

        <div class="hero-text">
            {{ translate('message.status_report') }}
        </div>

        <div class="row large-half-columns">
            <div class="col-md-6">
                <div class="widget">
                    <h3>{{ translate('message.overall_status') }}</h3>
                    <div class="flex flex-center">
                        <traffic-light
                                :value="projectTrafficLight"
                                size="large"
                                :editable="editable"
                                @input="onTrafficLightUpdate"/>
                    </div>

                    <h4>{{ translate('message.tasks_status') }}</h4>
                    <progress-bar-chart :series="tasksStatusSeries"/>
                    <br/>

                    <h4>{{ translate('message.tasks_condition') }}</h4>
                    <progress-bar-chart
                            :series="tasksTrafficLightSeries"
                            :options="{labels: {enabled: false}}"/>
                    <br/>

                    <div class="checkbox-input clearfix">
                        <template v-if="editable">
                            <input
                                    :value="value.projectActionNeeded"
                                    id="projectActionNeeded"
                                    type="checkbox"
                                    @input="onActionNeededUpdate"/>
                        </template>
                        <template v-else>
                            <input
                                    :value="report.projectActionNeeded"
                                    type="checkbox"
                                    :disabled="true"/>
                        </template>
                        <label class="no-margin-bottom" for="projectActionNeeded">{{ translate('message.action_needed') }}</label>
                        <error :at-path="projectActionNeeded"/>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <h3>{{ translate('message.project_trend') }}</h3>
                    <h4>{{ translate('message.current_date') }}: {{ report.createdAt | date }}</h4>

                    <status-report-trend-chart
                            v-if="trendChartData.length > 0 && !fetchingTrendChart"
                            :data="trendChartData"
                            :light="projectTrafficLight"
                            :labels="trendChartLabels"
                            :point-color="trendChartPointColor"/>
                    <div class="trend-no-results" v-else>{{ translate('message.not_enough_data') }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form">
                    <div class="form-group last-form-group">
                        <template v-if="editable">
                            <editor
                                    :value="value.comment"
                                    label="placeholder.comment"
                                    @input="onCommentUpdate"/>
                            <error at-path="comment"/>
                        </template>
                        <template v-else-if="report.comment">
                            <br/>
                            <h4>{{ translate('message.comment') }}</h4>
                            <div v-html="report.comment"></div>
                        </template>
                    </div>
                    <!-- /// End Project Staus Comment /// -->
                </div>
            </div>
        </div>

        <hr class="double">

        <template v-if="schedule">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.schedule') }}</h3>
                    <br/>
                    <status-report-schedule
                            :base-start-at="schedule.base.startAt"
                            :base-finish-at="schedule.base.finishAt"
                            :base-duration-days="schedule.base.durationDays"
                            :forecast-start-at="schedule.forecast.startAt"
                            :forecast-finish-at="schedule.forecast.finishAt"
                            :forecast-duration-days="schedule.forecast.durationDays"
                            :actual-start-at="schedule.actual.startAt"
                            :actual-finish-at="schedule.actual.finishAt"
                            :actual-duration-days="schedule.actual.durationDays"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <div class="row statuses min-status" v-if="progress">
            <div class="col-md-4">
                <div class="status">
                    <circle-chart
                            :bgStrokeColor="options.backgroundColor"
                            :percentage="projectPlannedProgress"
                            :title="translate('message.planned_progress')"
                            class="left center-content"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status">
                    <circle-chart
                            :bgStrokeColor="options.backgroundColor"
                            :percentage="progress.tasks"
                            :title="translate('message.task_status')"
                            class="left center-content"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status">
                    <circle-chart
                            :bgStrokeColor="options.backgroundColor"
                            :percentage="progress.costs"
                            :title="translate('message.cost_status')"
                            class="left center-content"/>
                </div>
            </div>
        </div>

        <hr class="double">

        <template v-if="isPhasesAndMilestoneModuleActive && (phases || milestones)">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.phases_and_milestones') }}</h3>
                    <traffic-light :value="projectTrafficLight"/>

                    <br/>

                    <status-report-timeline
                            :phases="phases"
                            :milestones="milestones"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <template v-if="isInternalCostsModuleActive && internalCostsGraphData">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.internal_costs') }}</h3>
                    <div class="marginbottom20">
                        <traffic-light :value="internalCostsTrafficLight"/>
                    </div>

                    <chart :data="internalCostsGraphData"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <template v-if="isExternalCostsModuleActive && externalCostsGraphData">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.external_costs') }}</h3>
                    <div class="marginbottom20">
                        <traffic-light :value="externalCostsTrafficLight"/>
                    </div>

                    <chart :data="externalCostsGraphData"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <template v-if="isRiskAndOpportunitiesModuleActive">
            <div class="row ro-columns large-half-columns">
                <div class="col-md-6 dark-border-right">
                    <opportunities-grid :value="opportunitiesGrid" :currency="currency"/>
                </div>

                <div class="col-md-6">
                    <risks-grid :value="risksGrid" :currency="currency"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <template v-if="isTodosModuleActive">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.todos') }}</h3>
                    <status-report-todos :items="todosItems"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <template v-if="isDecisionsModuleActive">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="margintop0">{{ translate('message.decisions') }}</h3>
                    <status-report-decisions :items="decisionsItems"/>
                </div>
            </div>

            <hr class="double">
        </template>

        <div class="row" v-if="isInfosModuleActive">
            <div class="col-md-12">
                <h3 class="margintop0">{{ translate('message.infos') }}</h3>
                <status-report-infos :items="infosItems"/>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapActions} from 'vuex';
    import 'jquery-match-height/jquery.matchHeight.js';
    import CircleChart from '../../../_common/_charts/CircleChart';
    import Chart from '.././../Charts/CostsChart.vue';
    import RiskList from '../../Risks/RiskList';
    import OpportunityList from '../../Opportunities/OpportunityList';
    import RiskSummary from '../../Risks/RiskSummary';
    import OpportunitySummary from '../../Opportunities/OpportunitySummary';
    import DownloadIcon from '../../../_common/_icons/DownloadIcon';
    import AtIcon from '../../../_common/_icons/AtIcon';
    import Editor from '../../../_common/Editor';
    import TrafficLight from '../../../_common/TrafficLight';
    import OpportunitiesGrid from './OpportunitiesGrid';
    import RisksGrid from './RisksGrid';
    import colors from '../../../../util/colors';
    import ProgressBarChart from '../../../_common/_charts/ProgressBarChart';
    import StatusReportTodos from './Todos';
    import StatusReportDecisions from './Decisions';
    import StatusReportTrendChart from './TrendChart';
    import Error from '../../../_common/_messages/Error';
    import StatusReportSchedule from './Schedule';
    import StatusReportTimeline from './Timeline';
    import StatusReportInfos from './Infos';
    import {
        MODULE_PHASES_AND_MILESTONES,
        MODULE_INTERNAL_COSTS,
        MODULE_EXTERNAL_COSTS,
        MODULE_RISKS_AND_OPPORTUNITIES,
        MODULE_TODOS,
        MODULE_DECISIONS,
        MODULE_INFOS,
    } from '../../../../helpers/project-module';
    import EventBus from '../../../../eventBus';
    import {themes} from '../../../../util/theme';

    export default {
        name: 'status-report',
        props: {
            report: {
                type: Object,
                required: true,
            },
            value: {
                type: Object,
                required: false,
                default: () => {},
            },
            editable: {
                type: Boolean,
                required: false,
                default: true,
            },
        },
        components: {
            StatusReportInfos,
            StatusReportTimeline,
            StatusReportSchedule,
            Error,
            StatusReportTrendChart,
            StatusReportDecisions,
            StatusReportTodos,
            ProgressBarChart,
            CircleChart,
            RiskList,
            RiskSummary,
            OpportunitySummary,
            OpportunityList,
            DownloadIcon,
            AtIcon,
            Chart,
            Editor,
            OpportunitiesGrid,
            RisksGrid,
            TrafficLight,
        },
        created() {
            this.getProjectUsers({id: this.$route.params.id});
            this.fetchStatusReportTrendGraph(this.report);
        },
        watch: {
            report: {
                handler(value, oldValue) {
                    this.fetchStatusReportTrendGraph(value);
                },
            },
            deep: true,
        },
        methods: {
            ...mapActions([
                'setMilestonesFilters',
                'getProjectMilestones',
                'getStatusReportTrendGraph',
                'createStatusReport',
                'getProjectUsers',
                'resetStatusReportTrendGraph',
            ]),
            fetchStatusReportTrendGraph(report) {
                let data = {
                    id: this.$route.params.id,
                    before: report.createdAt,
                };

                if (!report.id) {
                    data.includeCurrent = 1;
                }

                this.getStatusReportTrendGraph(data);
            },
            onActionNeededUpdate(event) {
                if (!this.editable) {
                    return;
                }

                this.$emit('input', Object.assign({}, this.value, {projectActionNeeded: event.target.checked}));
            },
            onCommentUpdate(value) {
                if (!this.editable) {
                    return;
                }

                this.$emit('input', Object.assign({}, this.value, {comment: value}));
            },
            onTrafficLightUpdate(value) {
                if (!this.editable) {
                    return;
                }

                let colors = [themes.light.lightRed, themes.light.lightYellow, themes.light.lightGreen];
                let currentDataChartLenght = this.trendChartLabels.length - 1;
                let prevReportStatus = this.trendChartData[currentDataChartLenght-1];
                this.trendChartData[currentDataChartLenght] = value + prevReportStatus - 1;
                this.trendChartPointColor[currentDataChartLenght] = colors[value];
                EventBus.$emit('updateChart');
                this.$emit('input', Object.assign({}, this.value, {projectTrafficLight: value}));
            },
            isModuleActive(module) {
                return this.report && this.report.modules && this.report.modules.indexOf(module) >= 0;
            },
        },
        computed: {
            ...mapGetters([
                'project',
                'projectMilestones',
                'statusReportTrendGraph',
            ]),
            isPhasesAndMilestoneModuleActive() {
                return this.isModuleActive(MODULE_PHASES_AND_MILESTONES);
            },
            isInternalCostsModuleActive() {
                return this.isModuleActive(MODULE_INTERNAL_COSTS);
            },
            isExternalCostsModuleActive() {
                return this.isModuleActive(MODULE_EXTERNAL_COSTS);
            },
            isRiskAndOpportunitiesModuleActive() {
                return this.isModuleActive(MODULE_RISKS_AND_OPPORTUNITIES);
            },
            isTodosModuleActive() {
                return this.isModuleActive(MODULE_TODOS);
            },
            isInfosModuleActive() {
                return this.isModuleActive(MODULE_INFOS);
            },
            isDecisionsModuleActive() {
                return this.isModuleActive(MODULE_DECISIONS);
            },
            projectName() {
                return this.report.projectName;
            },
            weekNumber() {
                return this.report.weekNumber;
            },
            projectTrafficLight() {
                return this.editable ? this.value.projectTrafficLight : this.report.projectTrafficLight;
            },
            snapshot() {
                return this.report.information;
            },
            tasksStatusSeries() {
                return [
                    {
                        name: 'label.open',
                        value: this.snapshot.tasks.total.status.opened,
                        color: '#646EA0',
                    }, {
                        name: 'label.executing',
                        value: this.snapshot.tasks.total.status.executing,
                        color: '#465079',
                    }, {
                        name: 'label.closed',
                        value: this.snapshot.tasks.total.status.closed,
                        color: '#232D4B',
                    },
                ];
            },
            tasksTrafficLightSeries() {
                let data = [];

                if (this.snapshot.tasks.total.trafficLight.green > 0) {
                    data.push({
                        name: 'color_status.finished',
                        value: this.snapshot.tasks.total.trafficLight.green,
                        color: colors.trafficLight.green,
                    });
                }

                if (this.snapshot.tasks.total.trafficLight.yellow > 0) {
                    data.push({
                        name: 'color_status.in_progress',
                        value: this.snapshot.tasks.total.trafficLight.yellow,
                        color: colors.trafficLight.yellow,
                    });
                }

                if (this.snapshot.tasks.total.trafficLight.red > 0) {
                    data.push({
                        name: 'color_status.not_started',
                        value: this.snapshot.tasks.total.trafficLight.red,
                        color: colors.trafficLight.red,
                    });
                }

                return data;
            },
            schedule() {
                return {
                    base: {
                        startAt: this.snapshot.schedule.scheduled.startAt,
                        finishAt: this.snapshot.schedule.scheduled.finishAt,
                        durationDays: this.snapshot.schedule.scheduled.durationDays,
                    },
                    forecast: {
                        startAt: this.snapshot.schedule.forecast.startAt,
                        finishAt: this.snapshot.schedule.forecast.finishAt,
                        durationDays: this.snapshot.schedule.forecast.durationDays,
                    },
                    actual: {
                        startAt: this.snapshot.schedule.actual.startAt,
                        finishAt: this.snapshot.schedule.actual.finishAt,
                        durationDays: this.snapshot.schedule.actual.durationDays,
                    },
                };
            },
            progress() {
                return {
                    tasks: Math.round(this.snapshot.tasks.progress),
                    costs: Math.round(this.snapshot.costs.progress),
                };
            },
            projectPlannedProgress() {
                return this.snapshot.plannedProgress;
            },
            internalCostsTrafficLight() {
                return this.snapshot.costs.internal.total.trafficLight;
            },
            externalCostsTrafficLight() {
                return this.snapshot.costs.external.total.trafficLight;
            },
            internalCostsGraphData() {
                let data = {};
                this.snapshot.costs.internal.graphs.byPhase.forEach((row) => {
                    data[row.name] = row.values;
                });

                return data;
            },
            externalCostsGraphData() {
                let data = {};
                this.snapshot.costs.external.graphs.byPhase.forEach((row) => {
                    data[row.name] = row.values;
                });

                return data;
            },
            opportunitiesGrid() {
                return {
                    top: this.snapshot.opportunities.topItem,
                    items: this.snapshot.opportunities.items,
                    summary: {
                        potentialCost: this.snapshot.opportunities.total.potentialCost,
                        potentialTime: this.snapshot.opportunities.total.potentialTime,
                        measuresCount: this.snapshot.opportunities.total.measuresCount,
                        measuresCost: this.snapshot.opportunities.total.measuresCost,
                    },
                };
            },
            risksGrid() {
                return {
                    top: this.snapshot.risks.topItem,
                    items: this.snapshot.risks.items,
                    summary: {
                        potentialCost: this.snapshot.risks.total.potentialCost,
                        potentialDelay: this.snapshot.risks.total.potentialDelay,
                        measuresCount: this.snapshot.risks.total.measuresCount,
                        measuresCost: this.snapshot.risks.total.measuresCost,
                    },
                };
            },
            todosItems() {
                return this.snapshot.todos
                    ? this.snapshot.todos.items
                    : [];
            },
            decisionsItems() {
                return this.snapshot.decisions
                    ? this.snapshot.decisions.items
                    : [];
            },
            infosItems() {
                return this.snapshot.infos
                    ? this.snapshot.infos.items
                    : [];
            },
            trendChartData() {
                return this.statusReportTrendGraph.map(data => {
                    return data.value;
                });
            },
            trendChartPointColor() {
                return this.statusReportTrendGraph.map(data => {
                    return colors.trafficLight[data.color];
                });
            },
            trendChartLabels() {
                return this.statusReportTrendGraph.map(data => {
                    return data.label;
                });
            },
            phases() {
                if (!this.snapshot.phases) {
                    return [];
                }

                return this.snapshot.phases.items;
            },
            milestones() {
                if (!this.snapshot.milestones || !this.snapshot.milestones.items) {
                    return [];
                }

                return this.snapshot.milestones.items.filter((milestone) => {
                    return milestone.isKeyMilestone;
                });
            },
            currency() {
                return this.snapshot.currency.symbol;
            },
        },
        data() {
            return {
                projectId: this.$route.params.id,
                actionNeeded: null,
                comment: '',
                milestoneColorClass: this.$theme.second,
                options: {
                    backgroundColor: this.$theme.darker,
                },
            };
        },
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
    @import '~theme/variables';
    @import '../../../../css/_mixins';

    .header {
        justify-content: center;
        text-align: center;

        h1 {
            padding-bottom: 20px;

            span {
                font-size: 0.75em;
                display: block;
                margin-top: 10px;
            }
        }
    }

    .hero-text {
        font-size: 3em;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 30px;
    }

    .large-half-columns {
        @media (max-width: 1600px) {
            .col-md-6 {
                width: 100%;
                margin-bottom: 20px;

                &.dark-border-right {
                    border-right: none;
                }

                &:last-child {
                    margin-bottom: 0;
                }
            }
        }
    }

    .widget {
        background-color: $darkColor;
        padding: 25px 20px;
        height: 360px;

        h3, h4 {
            margin: 0 0 10px;
            text-align: center;
        }
    }

    hr.double {
        margin: 40px 0;
    }

    .status-bar {
        margin-bottom: 30px;

        .bar {
            height: 30px;
            line-height: 30px;
            text-align: center;
            color: $lightColor;
            font-weight: 500;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    }

    .task-range-slider {
        margin-bottom: -9px;
    }

    .statuses {
        .status {
            max-width: 400px;
            margin: 20px auto 0;
            padding: 25px 20px;

            .chart {
                .text {
                    .title {
                        font-size: 12px;
                    }
                }

                &.center-content {
                    display: block;
                }
            }

            &:last-child {
                margin-bottom: 20px;
            }
        }
    }

    .dark-border-right {
        border-right: 1px solid $darkerColor;
    }

    .ro-columns {
        @media(min-width: 1601px) {
            > .col-md-6 {
                &:first-child {
                    padding-right: 30px;
                }

                &:last-child {
                    padding-left: 30px;
                }
            }
        }
    }

    .entry-responsible {
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 10px;
        line-height: 1.5em;
        margin: 20px 0;

        b {
            display: block;
            font-size: 12px;
        }
    }

    .cell-wrap {
        white-space: normal;
    }

    .cell-large {
        text-transform: none;
    }

    .table-small > thead > tr > th {
        height: 60px;
        padding: 0 15px;
    }

    .status-bar {
        min-width: 10%;
        max-width: 90%;
        display: inline-table;
    }

    .center-content {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .min-status {
        min-width: 716px;
    }

    .trend-no-results {
        text-align: center;
        color: $middleColor;
        min-height: 80%;
        line-height: 20em;
    }
</style>
