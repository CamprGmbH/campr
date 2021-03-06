<template>
    <div class="project-organization page-section">
        <modal v-if="showModal" @close="showModal = false">
            <p class="modal-title">{{ translate('title.add_distribution_list') }}</p>
            <div class="form-group">
                <input-field v-model="distributionTitle" type="text" v-bind:label="translate('label.distribution_list_title')"></input-field>
                <error
                    v-if="validationMessages.name && validationMessages.name.length"
                    v-for="message in validationMessages.name"
                    :message="message" />
            </div>
            <member-search v-model="selectedDistribution" v-bind:placeholder="translate('placeholder.search_members')" v-bind:singleSelect="false"></member-search>
            <div class="flex flex-space-between">
                <a href="javascript:void(0)" @click="showModal = false" class="btn-rounded btn-auto">{{ translate('button.cancel') }}</a>
                <a href="javascript:void(0)" @click="createDistributionList()" class="btn-rounded btn-auto second-bg">{{ translate('button.create_distribution') }} +</a>
            </div>
        </modal>

        <modal v-if="showDeleteMemberModal" @close="showDeleteMemberModal = false">
            <p class="modal-title">{{ translate('message.delete_team_member') }}</p>
            <div class="flex flex-space-between">
                <a href="javascript:void(0)" @click="showDeleteMemberModal = false" class="btn-rounded btn-auto">{{ translate('message.no') }}</a>
                <a href="javascript:void(0)" @click="deleteMember()" class="btn-rounded btn-empty btn-auto danger-color danger-border">{{ translate('message.yes') }}</a>
            </div>
        </modal>

        <can role="roles.project_manager|roles.project_sponsor" :subject="project" silent>
            <div class="header flex flex-space-between">
                <h1>{{ translate('message.project_organization') }}</h1>
                <div class="flex flex-v-center">
                    <router-link :to="{name: 'project-organization-edit'}" class="btn-rounded btn-auto second-bg">{{ translate('message.edit_project_organization') }}
                    </router-link>
                </div>
            </div>
        </can>

        <div class="team-graph">
            <project-organization-tree />
        </div>
        <div class="flex flex-space-between actions">
            <member-search ref="gridMemberSearch" v-model="gridList" v-bind:placeholder="translate('placeholder.search_members')"></member-search>
            <div class="flex">
                <button @click="clearFilters" class="btn-rounded btn-auto second-bg">{{ translate('button.clear_filters') }}</button>
                <a
                        v-if="canEditProject"
                        href="javascript:void(0)"
                        class="btn-rounded btn-auto second-bg"
                        @click="showWorkspaceMemberInviteModal = true">
                    {{ translate('label.invite_workspace_member') }}
                </a>
                <a
                        v-if="canEditProject"
                        href="javascript:void(0)"
                        class="btn-rounded btn-empty btn-auto"
                        @click="showModal = true">{{ translate('button.create_distribution') }}</a>
            </div>
        </div>
        <div class="team-list">
            <scrollbar class="customScrollbar">
                <div class="scroll-wrapper">
                    <table class="table table-striped table-responsive">
                        <thead>
                            <!-- <tr v-if='project.distributionLists'>
                                <th colspan="10"></th>
                                <th class="text-center " :colspan="project.distributionLists.length">{{ translate('table_header_cell.distribution_lists') }}</th>
                                <th></th>
                            </tr> -->
                            <tr>
                                <th class="avatar"></th>
                                <th>{{ translate('table_header_cell.company') }}</th>
                                <th>{{ translate('table_header_cell.name') }}</th>
                                <th>{{ translate('table_header_cell.role') }}</th>
                                <th>{{ translate('table_header_cell.subteam') }}</th>
                                <th>{{ translate('table_header_cell.department') }}</th>
                                <th>{{ translate('table_header_cell.contact') }}</th>
                                <th class="text-center switchers" v-if="canEditProject">{{ translate('table_header_cell.rasci') }}</th>
                                <th class="text-center switchers" v-if="canEditProject && project.distributionLists" v-for="dl in project.distributionLists">
                                    <span v-if="dl.sequence === -1">{{ translate(dl.name) }}</span>
                                    <span v-else>{{ dl.name }}</span>
                                </th>
                                <th v-if="canEditProject">{{ translate('table_header_cell.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in projectUsers.items">
                                <td class="text-center">
                                    <user-avatar
                                            :url="item.userAvatarUrl"
                                            :name="item.userFullName"/>
                                </td>
                                <td>{{ item.userCompanyName ? item.userCompanyName : '-' }}</td>
                                <td>{{ item.userFullName }}</td>
                                <td>
                                    <span v-for="(role, index) in item.projectRoleNames">
                                        {{ translate(role) }}<span v-if="index < item.projectRoleNames.length - 1">,</span>
                                    </span>
                                </td>
                                <td>
                                    <span v-for="(subteamName, index) in item.subteamNames">
                                        {{ subteamName }}<span v-if="index < item.subteamNames.length - 1">,</span>
                                    </span>
                                </td>
                                <td>
                                    <span v-for="(departmentName, index) in item.projectDepartmentNames">
                                        {{ departmentName }}<span v-if="index < item.projectDepartmentNames.length - 1">,</span>
                                    </span>
                                </td>
                                <td>
                                    <social-links align="left" size="20px" v-bind:facebook="item.userFacebook" v-bind:twitter="item.userTwitter" v-bind:linkedin="item.userLinkedIn" v-bind:gplus="item.userGplus" v-bind:email="item.userEmail" v-bind:phone="item.userPhone"></social-links>
                                </td>
                                <td class="text-center switchers" v-if="canEditProject">
                                    <switches
                                            @input="updateUserRASCI(item)"
                                            :disabled="item.isProjectManager || item.isProjectSponsor"
                                            :value="item.isRASCI"/>
                                </td>
                                <td class="text-center switchers" v-if="canEditProject" v-for="dl in project.distributionLists" :key="dl.id+'-'+item.user.id">
                                    <switches
                                            @input="updateDistributionItem(item, dl)"
                                            :value="inDistributionList(item.user, dl)"/>
                                </td>
                                <td v-if="canEditProject">
                                    <router-link :to="{name: 'project-organization-view-member', params: {id: projectId, userId: item.id} }" class="btn-icon">
                                        <view-icon fill="second-fill"></view-icon>
                                    </router-link>
                                    <button @click="initDeleteMemberModal(item)" type="button" class="btn-icon"><delete-icon fill="danger-fill"></delete-icon></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </scrollbar>

            <div class="flex flex-direction-reverse flex-v-center">
                <pagination
                        :value="activePage"
                        :number-of-pages="numberOfPages"
                        @input="changePage"/>
            </div>
        </div>

        <workspace-member-invite-modal v-if="showWorkspaceMemberInviteModal" @close="showWorkspaceMemberInviteModal = false" />
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';
import MemberBadge from '../_common/MemberBadge';
import SocialLinks from '../_common/SocialLinks';
import InputField from '../_common/_form-components/InputField';
import MemberSearch from '../_common/MemberSearch';
import Switches from '../3rdparty/vue-switches';
import Modal from '../_common/Modal';
import EditIcon from '../_common/_icons/EditIcon';
import DeleteIcon from '../_common/_icons/DeleteIcon';
import ViewIcon from '../_common/_icons/ViewIcon';
import Error from '../_common/_messages/Error.vue';
import WorkspaceMemberInviteModal from './Organization/WorkspaceMemberInviteModal.vue';
import ProjectOrganizationTree from './ProjectOrganizationTree';
import UserAvatar from '../_common/UserAvatar';
import Pagination from '../_common/Pagination';

export default {
    components: {
        Pagination,
        UserAvatar,
        MemberBadge,
        SocialLinks,
        InputField,
        Switches,
        Modal,
        MemberSearch,
        EditIcon,
        DeleteIcon,
        ViewIcon,
        Error,
        WorkspaceMemberInviteModal,
        ProjectOrganizationTree,
    },
    methods: {
        ...mapActions([
            'getProjectById', 'createDistribution', 'updateProjectUser', 'deleteTeamMember',
            'getProjectUsers', 'addToDistribution', 'removeFromDistribution',
        ]),
        changePage: function(page) {
            this.activePage = page;
            this.getProjectUsers({id: this.$route.params.id, page: page, users: this.gridList});
        },
        toggleTeam: function(id) {
            this.showTeam = Object.assign({}, this.showTeam, {[id]: !this.showTeam[id]});
        },
        toggleActivation: function(item) {
            item.checked = !item.checked;
        },
        createDistributionList: function() {
            let data = {
                name: this.distributionTitle,
                sequence: 0,
                position: 0,
                projectId: this.$route.params.id,
                users: this.selectedDistribution,
            };
            this.createDistribution(data)
                .then(
                    (data) => {
                        if (!data.error) {
                            this.showModal = false;
                            this.distributionTitle = '';
                        } else {
                            this.$flashError('message.unable_to_save');
                        }
                    },
                    () => {
                        this.$flashError('message.unable_to_save');
                    }
                );
        },
        inDistributionList: function(userId, distribution) {
            for (let i = 0; i < distribution.users.length; i++) {
                if (distribution.users[i].id === userId) {
                    return true;
                }
            }
            return false;
        },
        updateUserRASCI(item) {
            this.updateProjectUser({
                id: item.id,
                showInRasci: !item.showInRasci,
            });

            this.getProjectUsers({id: this.$route.params.id, page: this.activePage});
        },
        updateDistributionItem: function(item, distribution) {
            const self = this;
            this.inDistributionList(item.user, distribution)
                ? self.removeFromDistribution({id: distribution.id, user: item.user})
                : self.addToDistribution({id: distribution.id, user: item.user});
        },
        clearFilters: function() {
            this.$refs.gridMemberSearch.clearValue();
        },
        initDeleteMemberModal: function(member) {
            this.showDeleteMemberModal = true;
            this.memberId = member.id;
        },
        deleteMember: function() {
            this.deleteTeamMember(this.memberId);
            this.showDeleteMemberModal = false;
            this.memberId = null;
        },
    },
    created() {
        this.getProjectById(this.$route.params.id);
        this.getProjectUsers({id: this.$route.params.id, page: this.activePage});
    },
    computed: {
        ...mapGetters({
            project: 'project',
            projectUsers: 'projectUsers',
            validationMessages: 'validationMessages',
            distributionList: 'distributionList',
        }),
        canEditProject() {
            return this.$can('roles.project_manager|roles.project_sponsor', this.project);
        },
        numberOfPages: function() {
            if (!this.projectUsers || !this.projectUsers.totalItems) {
                return 1;
            }

            return Math.ceil(this.projectUsers.totalItems / this.perPage);
        },
        perPage: function() {
            if (!this.projectUsers || !this.projectUsers.pageSize) {
                return 1;
            }

            return this.projectUsers.pageSize;
        },
    },
    data() {
        return {
            showWorkspaceMemberInviteModal: false,
            selectedDistribution: [],
            distributionLists: [],
            gridList: [],
            activePage: 1,
            showTeam: {},
            showModal: false,
            projectId: this.$route.params.id,
            showDeleteMemberModal: false,
            memberId: null,
            projectItem: {},
        };
    },
    watch: {
        distributionList(value) {
            this.getProjectById(this.$route.params.id);
        },
        gridList(value) {
            this.getProjectUsers({id: this.$route.params.id, page: this.activePage, users: this.gridList});
        },
        selectedDistribution(value) {
            let distribution = [];
            let selected = this.selectedDistribution;
            this.projectUsers.items.map(function(user) {
                if (selected.indexOf(user.user) > -1) distribution.push(user);
            });
            this.distributionLists = distribution;
        },
    },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style lang="scss">
    @import '../../css/page-section';

    .modal .modal-inner {
        width: 600px;

        .results.team {
            width: 600px;
            position: relative;
            top: 0 !important;
        }
    }

    .actions {
        .search{
            width: 420px;
        }

        .btn-rounded {
            margin-left: 20px;
        }

        @media (max-width:1500px) {
            .search {
                width: auto;
            }
        }
    }
</style>

<style scoped lang="scss">
    @import '../../css/common';
    @import '~theme/variables';
    @import '../../css/mixins';

    .modal {
        .modal-title {
            text-transform: uppercase;
            text-align: center;
            font-size: 18px;
            letter-spacing: 1.8px;
            font-weight: 500;
            margin-bottom: 40px;
        }

        .main-list {
            margin-bottom: 30px;

            .member {
                border-top: 1px solid $darkColor;

                .member-info {
                    p {
                        margin: 0;
                        text-transform: uppercase;
                    }

                    .title {
                        margin: 10px 0 5px;
                        letter-spacing: 0.2em;
                    }

                    .description {
                        font-size: 0.875em;
                        letter-spacing: 0.1em;
                    }
                }
            }
        }

        .results {
            width: 600px;
            position: relative;
            top: 0 !important;
        }
    }

    .project-organization {
        overflow-x: hidden;
    }

    .team-graph {
        margin: 0 auto;
        text-align: center;
        width: 100%;
        max-width: 100%;

        .vue-scrollbar__wrapper {
            padding-bottom: 50px;
            overflow: inherit;
        }

        .subteams-container {
            position: relative;
            margin-top: 55px;

            &.multiple-teams {
                &:before {
                    content: '';
                    width: 1px;
                    height: 30px;
                    background-color: $middleColor;
                    position: absolute;
                    top: -57px;
                    left: 50%;
                }
            }

            .subteam-wrapper {
                display: inline-block;
                position: relative;

                &:only-child {
                    &:before {
                        display:none;
                    }
                }

                /*&:first-child {*/
                    /*width: 50%;*/

                    /*&:before {*/
                        /*left: 50%;*/
                    /*}*/
                /*}*/

                &:last-child {
                    /*width: 50%;*/

                    &:before {
                        left: -50%;
                    }
                }

                &:before {
                    content: '';
                    width: 100%;
                    height: 1px;
                    background-color: $middleColor;
                    position: absolute;
                    top: -26px;
                }
            }
        }

        .flex-center {
            position: relative;
            margin-top: 45px;

            &:after {
                content: '';
                width: 1px;
                height: 30px;
                background-color: $middleColor;
                position: absolute;
                top: -70px;
                left: 50%;
            }

            &.social-links {
                margin-top: 5px;

                &:after {
                    display: none;
                }
            }
        }

        .member-block {
            position: relative;

            &:before {
                content: '';
                width: 100%;
                height: 1px;
                background-color: $middleColor;
                position: absolute;
                top: -40px;
                left: 0;
            }

            &:first-child {
                &:before {
                    width: 50%;
                    left: 50%;
                }
            }

            &:last-child {
                &:before {
                    width: 50%;
                }
            }

            &:only-child {
                &:before {
                    display: none;
                }
            }
        }
    }

    .search {
        position: relative;
        margin-bottom: 30px;

        .scroll-list {
            max-height: 200px;
        }

        .team {
            margin-top: 0;

            .checkbox-input {
                margin-top: 13px;
                margin-right: 10px;
            }

            .footer {
                margin: 0 -20px;
                padding: 17px 20px;
                border-top: 1px solid $mainColor;
            }

            .footer p {
                margin-bottom: 11px;
                font-size: 10px;
            }

            .footer a {
                text-transform: uppercase;
            }

            .footer .cancel {
                color: $middleColor;
            }

            .footer .show {
                color: $secondColor;
            }
        }
    }

    .member-badge-wrapper {
        position: relative;
    }

    .team {
        position: absolute;
        width: 420px;
        background: $darkColor;
        top: 100%;
        margin-top: 10px;
        padding: 0 20px;
        max-height: 400px;
        z-index: 10;
        box-shadow: 0 0 10px 0 $darkColor;

        .member {
            padding: 15px 0;
            border-top: 1px solid $mainColor;

            .avatar {
                width: 46px;
                height: 46px;
                @include border-radius(50%);
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center center;
            }

            .info {
                text-transform: uppercase;
                padding: 0 0 0 10px;

                .title {
                    font-size: 10px;
                    color: $lighterColor;
                    letter-spacing: 1.5px;
                    text-align: left;
                }

                .description {
                    font-size: 10px;
                    color: $middleColor;
                    letter-spacing: 1.2px;
                    text-align: left;
                }
            }

            .social-links {
                margin-top: 0;
            }

            &:first-child {
                border-top: none;
            }
        }
    }

    .member-img {
        flex: 1;
    }

    .member-row {
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .lead {
        margin: 0 auto 15px !important;
    }

    .team-list {
        overflow: hidden;
    }

    .btn-small {
        height: 30px;
        line-height: 30px;
        width: 161px;

        &.show-team {
            background: $middleColor;
        }
    }

    .actions {
        margin: 30px 0;
    }

    .table-wrapper {
        width: 100%;
        padding-bottom: 40px;
    }
</style>
