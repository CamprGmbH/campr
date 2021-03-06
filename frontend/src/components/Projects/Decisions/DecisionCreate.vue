<template>
    <div class="row">
        <div class="col-md-12">
            <div class="create-meeting page-section">
                <!-- /// Header /// -->
                <div class="header flex-v-center">
                    <div>
                        <router-link :to="{name: 'project-decisions'}" class="small-link">
                            <i class="fa fa-angle-left"></i>
                            {{ translate('message.back_to_decisions') }}
                        </router-link>
                        <h1 v-if="!isEdit">{{ translate('message.create_new_decision') }}</h1>
                        <h1 v-else>{{ translate('message.edit_decision') }}</h1>
                    </div>
                </div>
                <!-- /// End Header /// -->

                <div class="form">
                    <!-- /// Info Category /// -->
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-6">
                                <select-field
                                    :title="translate('placeholder.distribution_list')"
                                    :options="distributionListsForSelect"
                                    v-model="distributionList"
                                    :currentOption="distributionList" />
                                <error at-path="distributionList" />
                            </div>

                            <div class="col-md-6">
                                <select-field
                                    :title="translate('label.category')"
                                    :options="decisionCategoriesForSelect"
                                    v-model="details.decisionCategory"/>
                                <error at-path="decisionCategory"/>
                            </div>
                        </div>
                    </div>
                    <!-- /// End Info Category /// -->

                    <!-- /// Info Title and Description /// -->
                    <div class="form-group">
                        <input-field
                                type="text"
                                :label="translate('placeholder.decision_title')"
                                v-model="title"
                                :content="title"/>
                        <error at-path="title"/>
                    </div>
                    <div class="form-group">
                        <editor
                                v-model="description"
                                :label="'placeholder.decision_description'"/>
                        <error at-path="description"/>
                    </div>
                    <!-- /// End Info Title and Description /// -->

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-6">
                                <member-search
                                        v-model="responsible"
                                        :placeholder="translate('placeholder.responsible')"
                                        :singleSelect="true"/>
                                <error at-path="responsibility"/>
                            </div>
                            <div class="col-md-6">
                                <div class="input-holder right">
                                    <label class="active">{{ translate('label.due_date') }}</label>
                                    <date-field v-model="dueDate" v-on:input="checkData"/>
                                </div>
                                <error at-path="dueDate" v-bind:message="calendarCorrect"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <select-field
                                    :title="translate('message.status')"
                                    :options="decisionsStatusesOptions"
                                    v-model="done"/>
                            <error at-path="done"/>
                        </div>
                    </div>
                    <hr class="double">

                    <!-- /// Decision Attachments /// -->
                    <h3>{{ translate('message.attachments') }}</h3>
                    <attachments
                            v-model="medias"
                            :max-file-size="projectMaxUploadFileSize"
                            @uploading="onUploading"/>
                    <!-- /// End Task Attachments /// -->
                    <hr class="double">

                    <!-- /// Actions /// -->
                    <div class="flex flex-space-between">
                        <router-link :to="{name: 'project-decisions'}" class="btn-rounded btn-auto btn-auto disable-bg">
                            {{ translate('button.cancel') }}
                        </router-link>
                        <a
                                v-if="!isEdit && canSave"
                                @click="createNewDecision()"
                                class="btn-rounded btn-auto btn-auto second-bg">{{ translate('button.create_decision') }}</a>
                        <a
                                v-if="isEdit && canSave"
                                @click="saveDecision()"
                                class="btn-rounded btn-auto second-bg">{{ translate('button.save') }}</a>
                    </div>
                    <!-- /// End Actions /// -->
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import InputField from '../../_common/_form-components/InputField';
    import SelectField from '../../_common/_form-components/SelectField';
    import MemberSearch from '../../_common/MemberSearch';
    import {mapActions, mapGetters} from 'vuex';
    import moment from 'moment';
    import Error from '../../_common/_messages/Error.vue';
    import Editor from '../../_common/Editor.vue';
    import Attachments from '../../_common/Attachments';
    import DateField from '../../_common/_form-components/DateField';
    import {createFormData} from '../../../helpers/decision';
    import {calendarNotPast} from '../../../util/validator_helper';

    export default {
        components: {
            DateField,
            InputField,
            SelectField,
            MemberSearch,
            moment,
            Error,
            Editor,
            Attachments,
        },
        methods: {
            ...mapActions([
                'getDistributionLists',
                'getDecisionCategories',
                'createDecision',
                'getDecision',
                'editDecision',
                'emptyValidationMessages',
            ]),
            checkData: function(value) {
                const message = this.translate('before.now');
                this.calendarCorrect = calendarNotPast(message, value);
            },
            onUploading(uploading) {
                this.isUploading = uploading;
            },
            getData() {
                return {
                    project: this.$route.params.id,
                    distributionList: this.distributionList ? this.distributionList.key : null,
                    title: this.title,
                    description: this.description,
                    done: this.done.key,
                    decisionCategory: this.details.decisionCategory ? this.details.decisionCategory.key : null,
                    responsibility: this.responsible.length > 0 ? this.responsible[0] : null,
                    dueDate: this.dueDate ? moment(this.dueDate).format('DD-MM-YYYY') : null,
                    medias: this.medias,
                };
            },
            createNewDecision: function() {
                let formData = createFormData(this.getData());
                this.createDecision({
                    data: formData,
                    projectId: this.$route.params.id,
                }).then(
                    (response) => {
                        if (response.body && response.body.error && response.body.messages) {
                            this.$flashError('message.unable_to_save');
                            return;
                        }

                        this.$flashSuccess('message.saved');
                    },
                    () => {
                        this.$flashError('message.unable_to_save');
                    },
                );
            },
            saveDecision: function() {
                let formData = createFormData(this.getData());
                this.editDecision({
                    data: formData,
                    id: this.$route.params.decisionId,
                }).then(
                    (response) => {
                        if (response.body && response.body.error && response.body.messages) {
                            this.$flashError('message.unable_to_save');
                            return;
                        }

                        this.$flashSuccess('message.saved');
                    },
                    () => {
                        this.$flashError('message.unable_to_save');
                    },
                );
            },
        },
        computed: {
            ...mapGetters([
                'decisionCategoriesForSelect',
                'distributionListsForSelect',
                'validationMessages',
                'currentDecision',
                'decisionsStatusesForSelect',
                'currentDecisionStatusForSelect',
                'projectMaxUploadFileSize',
                'validationMessagesFor',
                'project',
            ]),
            canSave() {
                return !this.isUploading;
            },
            mediasValidationMessages() {
                let messages = this.validationMessagesFor('medias');
                let out = [];

                Object.keys(messages).forEach((index) => {
                    out[index] = messages[index].file;
                });

                return out;
            },
            decisionsStatusesOptions() {
                return this.decisionsStatusesForSelect.map((option) => {
                    return Object.assign({}, option, {label: this.translate(option.label)});
                });
            },
        },
        created() {
            this.getDecisionCategories();
            this.getDistributionLists({projectId: this.$route.params.id});
            if (this.$route.params.decisionId) {
                this.getDecision(this.$route.params.decisionId);
            }
        },
        beforeDestroy() {
            this.emptyValidationMessages();
        },
        watch: {
            currentDecision(value) {
                this.title = this.currentDecision.title;
                this.description = this.currentDecision.description;
                this.done = this.currentDecisionStatusForSelect;
                this.distributionList = value.distributionList
                    ? {key: value.distributionList, label: value.distributionListName}
                    : null;
                this.details.decisionCategory = this.currentDecision.decisionCategory
                    ? {key: this.currentDecision.decisionCategory, label: this.currentDecision.decisionCategoryName}
                    : null
                ;
                this.responsible.push(this.currentDecision.responsibility);
                this.dueDate = this.currentDecision.dueDate
                    ? moment(this.currentDecision.dueDate).toDate()
                    : null;
                this.medias = this.currentDecision.medias ? this.currentDecision.medias : [];
            },
        },
        data() {
            return {
                title: '',
                responsible: [],
                description: '',
                done: {
                    key: false,
                    label: this.translate('choices.undone'),
                },
                dueDate: null,
                details: {
                    decisionCategory: null,
                },
                distributionList: null,
                medias: [],
                isEdit: this.$route.params.decisionId,
                showSaved: false,
                isUploading: false,
                calendarCorrect: null,
            };
        },
    };
</script>
