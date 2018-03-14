<template>
    <div>
        <vue-chart
                chart-type="ColumnChart"
                :columns="columns"
                :rows="data | transform"
                :options="chartOptions">
        </vue-chart>
    </div>
</template>

<script>
    export default {
        name: 'charts-costs-chart',
        props: {
            options: {
                type: Object,
                required: false,
                default: () => {
                    return {
                        hAxis: {
                            textStyle: {
                                color: '#D8DAE5',
                            },
                        },
                        vAxis: {
                            title: '',
                            minValue: 0,
                            maxValue: 0,
                            textStyle: {
                                color: '#D8DAE5',
                            },
                        },
                        width: '100%',
                        height: 350,
                        curveType: 'function',
                        colors: ['#5FC3A5', '#A05555', '#646EA0', '#2E3D60', '#D8DAE5'],
                        backgroundColor: '#191E37',
                        titleTextStyle: {
                            color: '#D8DAE5',
                        },
                        legend: {
                            position: 'bottom',
                            maxLines: 5,
                        },
                        legendTextStyle: {
                            color: '#D8DAE5',
                        },
                    };
                },
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
        },
        filters: {
            transform(data) {
                if (!data) {
                    return [['', 0, 0, 0, 0]];
                }

                return _.map(data, (value, key) => {
                    return [
                        key,
                        parseFloat(value.base),
                        parseFloat(value.actual),
                        parseFloat(value.forecast),
                        parseFloat(value.remaining),
                    ];
                });
            },
        },
        computed: {
            chartOptions() {
                if (!this.title) {
                    return this.options;
                }

                return Object.assign({}, this.options, {title: Translator.trans(this.title)});
            },
        },
        data() {
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
            };
        },
    };
</script>