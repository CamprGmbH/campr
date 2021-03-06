<template>
    <div class="search">
        <div class="input-holder">
            <input type="text"
                   class="float-label"
                   :id="'input' + _uid"
                   :ref="`input${_uid}`"
                   autocomplete="off"
                   v-model="query"
                   @keydown.down="down"
                   @keydown.up="up"
                   @keydown.enter="hit"
                   @keydown.esc="reset"
                   @focus="focused = true"
                   @blur="focused = false"
                   @input="update"/>
            <label :class="{ 'active': isActive }" @click="focusInput()">{{ placeholder }}</label>
        </div>
        <i class="member-search-clear-button" @click="clearValue">×</i>
        <div class="results team" v-show="hasItems" :style="{bottom:resultsTeamBottom,top:resultsTeamTop}">
            <scrollbar class="members customScrollbar">
                <div class="member flex flex-v-center" v-for="item in items">
                    <div class="checkbox-input clearfix" :class="{'inactive': !item.checked}">
                        <input v-if="singleSelect" :id="'mid_' + item.id" type="radio" :name="item.userFullName"
                               :checked="item.checked" @click="toogleRadioButton(item)">
                        <input v-else="singleSelect" :id="'mid_' + item.id" type="checkbox" :name="item.userFullName"
                               :checked="item.checked" @click="toggleActivation(item)">
                        <label :for="'mid_' + item.id"></label>
                    </div>
                    <user-avatar
                            size="normal"
                            :name="item.userFullName"
                            :url="item.userAvatarUrl" />
                    <div class="info">
                        <p class="title">{{ item.userFullName }}</p>
                        <p class="description"><span v-for="roleName in item.projectRoleNames">{{ translate(roleName) }}, </span></p>
                    </div>
                </div>
            </scrollbar>
            <div class="footer">
                <div class="flex flex-space-between">
                    <a href="javascript:void(0)" @click="reset" class="cancel">{{ translate('button.cancel') }}</a>
                    <a v-if="singleSelect" href="javascript:void(0)" @click="updateSelected()" class="show">{{ translate('button.done') }}</a>
                    <a v-else="singleSelect" href="javascript:void(0)" @click="updateSelected()" class="show">{{ translate('button.show_selected') }}</a>
                </div>
            </div>
        </div>
        <div class="results team no-data" v-if="noData && query !== ''">
            <div>{{ translate('label.no_data') }}</div>
        </div>
        <p v-if="usersList && usersList.length" v-for="user in usersList" class="selected-item">
            <user-avatar
                    size="small"
                    :url="user.avatarUrl"
                    :name="user.userFullName"/>
            <span>{{ user.firstName }} {{ user.lastName }}</span>
            <a @click="removeSelectedOption(user.id)"> <i class="fa fa-times"></i></a>
        </p>
    </div>
</template>

<script>
    import VueTypeahead from 'vue-typeahead';
    import {mapActions, mapGetters} from 'vuex';
    import $ from 'jquery';
    import _ from 'lodash';
    import UserAvatar from '../_common/UserAvatar';

    export default {
        components: {
            UserAvatar,
        },
        extends: VueTypeahead,
        props: ['placeholder', 'singleSelect', 'value', 'selectedUser'],
        computed: {
            ...mapGetters(['users']),
            isActive: {
                get: function() {
                    return _.isEmpty(this.placeholder)
                        || !_.isEmpty(this.value)
                        || !_.isEmpty(this.query)
                        || this.focused;
                },
            },
            displaySelectedMembers() {
                if (!this.value || !this.value.length) {
                    return false;
                }

                return !!this.users.find((user) => {
                    return this.value.includes(user.id);
                });
            },
        },
        watch: {
            users(val) {
                if (this.displaySelectedMembers) {
                    this.usersList = this.users;
                }
            },
            value(val) {
                if (val.length && val[0] != null) {
                    this.getUsers({id: val});
                } else {
                    this.clearUsers();
                    this.usersList = [];
                    this.selectedUsers = [];
                    this.reset();
                }
            },
            hasItems(val) {
                if (val) {
                    let scrollTop = $(window)
                        .scrollTop();
                    let elementOffset = $(this.$el)
                        .offset().top;
                    let currentElementOffset = (elementOffset - scrollTop);

                    let windowInnerHeight = window.innerHeight;

                    if (windowInnerHeight - currentElementOffset < 260) {
                        this.resultsTeamBottom = '41px';
                        this.resultsTeamTop = 'auto';
                    } else {
                        this.resultsTeamBottom = 'auto';
                        this.resultsTeamTop = '41px';
                    }
                }
            },
        },
        methods: {
            ...mapActions(['getUsers', 'clearUsers']),
            toggleActivation(item) {
                item.checked = !item.checked;
            },
            toogleRadioButton(item) {
                this.items = this.items.map(item => {
                    item.checked = false;
                    return item;
                });
                item.checked = true;
            },
            prepareResponseData(data) {
                let items = data.items;
                if (!Array.isArray(items) || !items) {
                    return [];
                }
                let users = [];
                let selected = this.selectedUsers;
                items.map(function(user) {
                    let checked = false;
                    for (let i = 0; i < selected.length; i++) {
                        if (selected[i] === user.user) {
                            checked = true;
                        }
                        user.checked = checked;
                    }
                    users.push(user);
                });
                this.noData = users.length === 0;

                return users;
            },
            updateSelected() {
                let users = [];
                let selectedUsers = this.selectedUsers;
                this.items.map(function(user) {
                    if (user.checked) {
                        users.push(user.user);
                    } else if (selectedUsers.length > 0) {
                        let index = selectedUsers.indexOf(user.user);
                        if (index > -1) {
                            selectedUsers.splice(index, 1);
                        }
                    }
                });
                selectedUsers.map(function(id) {
                    if (users.indexOf(id) === -1) {
                        users.push(id);
                    }
                });
                this.selectedUsers = users;
                this.$emit('input', this.selectedUsers);
                const items = this.items;
                this.reset();
                if (this.singleSelect && items.length > 0) {
                    this.query = items.filter((item) => item.checked)[0].userFullName;
                }
            },
            clearValue() {
                this.query = '';
                this.items = [];
                this.noData = false;
                this.usersList = [];
                this.selectedUsers = [];
                this.updateSelected();
            },
            removeSelectedOption(id) {
                this.$emit('input', this.value.filter(item => parseInt(item, 10) !== parseInt(id, 10)));

                if (this.singleSelect) {
                    this.usersList = [];
                    this.selectedUsers = [];
                } else {
                    let indexTmp;
                    this.usersList.map(function(user, index) {
                        if (user.id == id) {
                            indexTmp = index;
                        }
                    });
                    this.usersList.splice(indexTmp, 1);
                    this.selectedUsers.splice(indexTmp, 1);
                }
            },
            focusInput() {
                $(this.$refs[`input${this._uid}`])
                    .focus();
            },
        },
        data() {
            return {
                src: Routing.generate('app_api_project_project_users', {id: this.$route.params.id}),
                queryParamName: 'search',
                noData: null,
                minChars: 1,
                selectedUsers: [],
                usersList: [],
                resultsTeamBottom: '',
                resultsTeamTop: '',
                focused: false,
            };
        },
        created() {
            this.clearUsers();
        },
        mounted() {
            if (this.selectedUser && this.value) {
                this.query = this.selectedUser;
                this.selectedUsers = this.value;

                this.getUsers({id: this.value});
            }
        },
    };
</script>

<style scoped lang="scss">
    @import '~theme/variables';
    @import '../../css/_mixins';

    .modal {
        .modal-title {
            text-transform: uppercase;
            text-align: center;
            font-size: 18px;
            letter-spacing: 1.8px;
            font-weight: 500;
            margin-bottom: 40px;
        }

        .input-holder {
            margin: 30px 0 0;

            &:first-of-type {
                margin-top: 0;
            }
        }

        .main-list .member {
            border-top: 1px solid $darkColor;
        }

        .results {
            width: 600px;
            .members {
                max-height: 265px;
            }
        }
    }

    .results {
        .members {
            max-height: 265px;
        }
    }

    .st0 {
        stroke: $lighterColor;
    }

    .search {
        position: relative;

        .scroll-list {
            max-height: 200px;
        }

        .team {
            margin-top: 0;

            .footer {
                margin: 0;
                padding: 17px 15px;
                border-top: 1px solid $mainColor;
            }

            .footer p {
                margin-bottom: 11px;
                font-size: 10px;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }

            .footer a {
                text-transform: uppercase;
                letter-spacing: 0.1em;
                @include transition(color, 0.3s, ease);
            }

            .footer .cancel {
                color: $middleColor;

                &:hover {
                    color: darken($middleColor, 15%);
                }
            }

            .footer .show {
                color: $secondColor;

                &:hover {
                    color: $secondDarkColor;
                }
            }
        }
        .member-search-clear-button {
            position: absolute;
            right: 20px;
            top: 13px;
            font-size: 1.5em;
            color: $dangerColor;
            cursor: pointer;
            font-style: normal;
        }
    }

    .member-badge-wrapper {
        position: relative;
    }

    .team {
        position: absolute;
        width: 420px;
        background: $darkColor;
        top: 40px;
        margin-top: 10px;
        max-height: 400px;
        z-index: 10;
        box-shadow: 0 2px 20px -2px $blackColor;

        &.no-data {
            width: 100% !important;
            position: absolute;
            padding: 5px 20px;
        }
    }

    .members {
        padding: 0 15px 0;
    }

    .member {
        padding: 15px 0;
        border-top: 1px solid $mainColor;

        &:first-child {
            border-top: none;
        }
    }

    .avatar {
        width: 46px;
        height: 46px;
        @include border-radius(50%);
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .info {
        text-transform: uppercase;
        padding: 0 0 0 10px;
    }

    .title {
        font-size: 10px;
        color: $lighterColor;
        letter-spacing: 1.5px;
    }

    .description {
        font-size: 10px;
        color: $middleColor;
        letter-spacing: 1.2px;
    }

    .selected-item {
        padding: 0 0 0 10px;
        background-color: $fadeColor;
        margin-top: 3px;
        color: $lighterColor;
        position: relative;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.1em;
        font-weight: 700;

        span {
            position: relative;
            top: 2px;
        }

        i.fa {
            position: absolute;
            right: 20px;
            top: 50%;
            margin-top: -5px;
            color: $dangerColor;
            cursor: pointer;
            @include transition(opacity, 0.2s, ease-in);

            &:hover,
            &:active {
                @include opacity(0.8);
            }
        }
    }
</style>
