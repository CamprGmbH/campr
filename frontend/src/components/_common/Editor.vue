<template>
    <div class="editor" :class="{disabled: disabled}">
        <span class="label" v-if="label">{{ translate(label) }}</span>
        <vue-editor
            :id="id"
            :value="value"
            @input="onInput"
            :disabled="disabled"
            :editorToolbar="toolbar" />
    </div>
</template>

<script>
    import {VueEditor} from 'vue2-editor';
    import Vue from 'vue';

    export default {
        name: 'editor',
        props: {
            id: {
                type: String,
                default: 'quill-container',
            },
            value: {
                type: String,
                required: false,
                default() {
                    return '';
                },
            },
            label: {
                type: String,
                required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false,
            },
            toolbar: {
                type: Array,
                required: false,
                default: () => {
                    return [
                        ['bold', 'italic', 'underline'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        ['image'],
                    ];
                },
            },
            options: {
                type: Object,
                required: false,
                default: () => {
                    return {};
                },
            },
            height: {
                type: String,
                required: false,
                default: '400px',
            },
        },
        components: {
            VueEditor,
        },
        filters: {
            trans: (str) => Vue.translate(str),
        },
        methods: {
            onInput(value) {
                this.$emit('input', value);
            },
        },
        mounted() {
            this.$nextTick(() => {
                this.$el.querySelector(`#${this.id}`).style.height = this.height;
            });
        },
    };
</script>

<style lang="scss">
    @import '~theme/variables';

    .editor {
        position: relative;
        clear: both;

        > span.label {
            top: 17px;
            position: absolute;
        }

        &.disabled {
            .ql-snow {
                opacity: .5;
                pointer-events: none !important;
            }
        }
    }

    .editor + .buttons {
        .btn-auto {
            margin:15px 10px 0 0;
        }
    }

    .ql-toolbar {
        border: none !important;
        border-bottom: 1px solid $middleColor !important;
        text-align: right;

        .ql-formats {
            color: $middleColor !important;

            .ql-picker {
                color: $middleColor !important;

                .ql-picker-label {
                    font-size: 12px;
                    font-weight: normal;
                }
            }
        }
    }

    .ql-snow {
        background-color: $darkColor;
        color: $lightColor;
        font-size: 10px;

        .ql-stroke {
            stroke: $middleColor !important;
            stroke-width: 1px !important;
        }
        .ql-fill {
            fill: $middleColor !important;
        }
    }

    .ql-editor {
        font-family: Poppins !important;
        font-size: 12px !important;
        line-height: 1.5em;

        span {
            background-color: transparent !important;
            color: $lightColor !important;
        }
    }

    .project-box {
        .ql-editor {
            padding: 12px 0;
        }
    }

    .ql-container {
        border: none !important;
    }
</style>

<style scoped lang="scss">
    @import '../../css/_variables';
    
    .editor {
        margin-top: 1em;

        .label {
            color: $lightColor;
            z-index: 2;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 500;
            margin: 0 0 0 15px;
        }
    }
</style>
