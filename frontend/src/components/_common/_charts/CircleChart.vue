<template>
    <div :data-percentage="percentage" class="chart relative" :id="'chart-' + _uid">
        <div class="text">
            <div class="title">{{ title }}</div>
            <div class="value">
                <div class="percentage">{{formatPercentage(percentage)}}</div>
                <div class="percentage-sign">%</div>
            </div>
        </div>
    </div>
</template>

<script>
import * as d3 from 'd3';

export default {
    name: 'circle-chart',
    props: {
        percentage: {},
        title: {},
        /* width: {
            default() {
                return 360;
            },
        },
        height: {
            default() {
                return 360;
            },
        }, */
        precision: {
            default: 0,
        },
        bgStrokeColor: {
            type: String,
            default: '#232D4B',
        },
        fgStrokeColor: {
            type: String,
            default: '#5FC3A5',
        },
        strokeWidth: {
            type: Number,
            default: 3,
        },
        animationDuration: {
            type: Number,
            required: false,
            default: 2048,
        },
    },
    methods: {
        init() {
            /* let width = parseInt(this.width, 10);
            let height = parseInt(this.width, 10);
            let radius = Math.min(width, height) / 2; */
            let width = this.$el.offsetWidth;
            let height = width;
            let radius = width / 2;

            const arc = d3
                .arc()
                .innerRadius(radius - this.strokeWidth)
                .outerRadius(radius - this.strokeWidth)
                .startAngle(0)
            ;

            d3.selectAll('#chart-' + this._uid + ' svg').remove();
            const svg = d3
                .select('#chart-' + this._uid)
                .insert('svg', ':first-child')
                /* .attr('width', width)
                .attr('height', height) */
                .attr('viewBox', '0 0 ' + Math.min(width, height) + ' ' + Math.min(width, height) )
                .attr('preserveAspectRatio', 'xMinYMin')
            ;

            const g = svg
                .append('g')
                /* .attr('transform', `translate(${width / 2}, ${height / 2})`) */
                .attr('transform', 'translate(' + Math.min(width, height) / 2 + ',' + Math.min(width, height) / 2 + ')')
            ;

            // draw main circle
            const main = g
                .append('path')
                .datum({endAngle: 0})
                .attr('fill', 'transparent')
                .attr('stroke-width', this.strokeWidth)
                .attr('stroke', this.bgStrokeColor)
                .attr('d', d => arc(d))
            ;

            setTimeout(() => {
                const interpolate = d3.interpolate(0, Math.PI * 2);

                main
                    .transition()
                    .duration(this.animationDuration)
                    .attrTween('d', d => {
                        return t => {
                            d.endAngle = interpolate(t);

                            return arc(d);
                        };
                    })
                ;
            }, this.animationDuration / 2);

            let percentage = parseInt(this.percentage, 10);
            if (isNaN(percentage)) {
                percentage = 0;
            }

            if (percentage) {
                // draw status arc/circle
                const progress = g
                    .append('path')
                    .datum({endAngle: 0})
                    .attr('fill', 'transparent')
                    .attr('stroke-width', this.strokeWidth)
                    .attr('stroke', this.fgStrokeColor)
                    .attr('d', d => arc(d))
                ;

                setTimeout(() => {
                    const interpolate = d3.interpolate(0, Math.PI * percentage / 50);

                    progress
                        .transition()
                        .duration(this.animationDuration)
                        .attrTween('d', d => {
                            return t => {
                                d.endAngle = interpolate(t);

                                return arc(d);
                            };
                        })
                    ;
                }, this.animationDuration);
            }
        },
        formatPercentage(percentage) {
            if (isNaN(percentage)) {
                return 0;
            }
            let pivotNumber = Math.pow(10, this.precision);
            return Math.floor(percentage * pivotNumber) / pivotNumber;
        },
    },
    watch: {
        percentage(value) {
            if (value === undefined) {
                return;
            }

            this.init();
        },
    },
    mounted() {
        this.init();
    },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
    @import '../../../css/_common';
    @import '~theme/variables';

    .chart {
        font-size: 22px;
        text-align: center;

        svg {
            display: inline-block;
            width: 100%;
            transform: rotate(-90deg);
        }

        .empty {
            stroke: $mainColor;
        }

        .full {
            stroke: $secondColor;
            stroke-dasharray: 0, 10000;
        }

        .text {
            position: absolute;
            left: 50%;
            top: 50%;
            padding: 0;
            margin: 0;
            transform: translate(-50%, -50%);
            color: $fgColor;
            line-height: 1em;

            .title {
                text-transform: uppercase;
                font-size: 9px;
                text-align: center;
                line-height: 1em;
            }

            .value {
                text-align: center;
                white-space: nowrap;
            }

            .percentage {
                display: inline-block;
                font-weight: 700;
                font-size: 34px;
                line-height: 34px;
            }

            .percentage-sign {
                display: inline-block;
                color: $middleColor;
                font-size: 12px;
                line-height: 22px;
                margin-left: -5px;
            }
        }

        &.dark-chart {
            .empty {
                stroke: $darkerColor;
            }
        }

        &.warning {
            .full {
                stroke: $warningColor;
            }
        }

        &.danger {
            .full {
                stroke: $dangerColor;
            }
        }
    }

    .widget.task-status-widget {
        @media (min-width:1024px) {
            .chart {
                .text {
                    .title {
                        font-size: 16px;
                    }

                    .percentage {
                        font-size: 85px;
                        line-height: 85px;
                    }

                    .percentage-sign {
                        font-size: 16px;
                        line-height: 50px;
                    }
                }
            }
        }
    }
</style>
