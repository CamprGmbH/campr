<template>
    <div class="cost-charts">
        <vue-chart
            chart-type="ColumnChart"
            :columns="columns"
            :rows="data | transform"
            :options="chartOptions">
        </vue-chart>
    </div>
</template>

<style lang="css">
    div.cost-charts svg > g:last-child > g:last-child { pointer-events: none }
</style>

<script>
    import _ from 'lodash';
    import {costChart as colors} from '../../../util/colors';

    export default {
        name: 'charts-costs-chart',
        props: {
            options: {
                type: Object,
                required: false,
                default: () => {},
            },
            data: {
                type: Object,
                required: false,
            },
            title: {
                type: String,
                required: false,
                default: null,
            },
            currency: {
                type: String,
                required: false,
                default: '',
            },
            theme: {
                type: String,
                required: false,
                default: 'default',
                validate(value) {
                    return ['default', 'print'].indexOf(value) >= 0;
                },
            },
        },
        filters: {
            transform(data) {
                if (!data) {
                    return [['', 0, 0, 0, 0]];
                }

                return _.map(data, (value, key) => {
                    return [
                        key,
                        _.toFinite(value.base),
                        _.toFinite(value.actual),
                        _.toFinite(value.forecast),
                        _.toFinite(value.remaining),
                    ];
                });
            },
        },
        computed: {
            chartOptions() {
                let options = Object.assign({}, this.themes[this.theme], this.options);
                if (!this.title) {
                    return options;
                }

                return Object.assign({}, options, {title: Translator.trans(this.title)});
            },
        },
        data() {
            let defaultOptions = {
                hAxis: {
                    textStyle: {
                        color: this.$theme.lighter,
                    },
                },
                vAxis: {
                    title: '',
                    minValue: 0,
                    maxValue: 0,
                    textStyle: {
                        color: this.$theme.lighter,
                    },
                },
                width: '100%',
                height: 350,
                curveType: 'function',
                colors: [colors.base, colors.actual, colors.forecast, colors.remaining],
                backgroundColor: this.$theme.dark,
                titleTextStyle: {
                    color: this.$theme.lighter,
                },
                legend: {
                    position: 'bottom',
                    maxLines: 5,
                },
                legendTextStyle: {
                    color: this.$theme.lighter,
                },
            };

            return {
                columns: [
                    {
                        'type': 'string',
                        'label': Translator.trans('message.total'),
                    }, {
                        'type': 'number',
                        'label': Translator.trans('label.base'),
                    }, {
                        'type': 'number',
                        'label': Translator.trans('label.actual'),
                    }, {
                        'type': 'number',
                        'label': Translator.trans('label.forecast'),
                    }, {
                        'type': 'number',
                        'label': Translator.trans('label.remaining'),
                    },
                ],
                themes: {
                    default: defaultOptions,
                    print: Object.assign({}, defaultOptions, {
                        hAxis: {
                            textStyle: {
                                color: '#000',
                            },
                        },
                        vAxis: {
                            title: '',
                            minValue: 0,
                            maxValue: 0,
                            textStyle: {
                                color: '#000',
                            },
                        },
                        height: 350,
                        backgroundColor: '#fff',
                        titleTextStyle: {
                            color: '#000',
                        },
                        legendTextStyle: {
                            color: '#000',
                        },
                    }),
                },
            };
        },
    };
</script>
