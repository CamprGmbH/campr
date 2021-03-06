import * as types from '../mutation-types';
import Vue from 'vue';

const state = {
    wbs: [],
};

/**
 * Gets stuff.
 *
 * @param {object} workPackage
 * @param {integer} level
 * @return {Array}
 */
function getPhasesAndMilestones(workPackage, level) {
    let out = [];

    workPackage
        .children
        .filter(wp => [0, 1].indexOf(wp.type) !== -1)
        .map(wp => {
            let newWp = Object.assign({}, wp, {level});

            out.push(newWp);
            out = out.concat(getPhasesAndMilestones(wp, level + 1));
        })
    ;

    return out;
}

const getters = {
    wbs: state => state.wbs,
    wbsPhasesAndMilestones: (state) => {
        if (!state.wbs || !state.wbs.children) {
            return [];
        }

        let out = [];

        state
            .wbs
            .children
            .filter(wp => [0, 1].indexOf(wp.type) !== -1)
            .map(wp => {
                let newWp = Object.assign({}, wp, {level: 0});
                out.push(newWp);
                out = out.concat(getPhasesAndMilestones(wp, 1));
            })
        ;

        return out;
    },
};

const actions = {
    getWBSByProjectID({commit}, id) {
        return Vue
            .http
            .get(Routing.generate('app_api_project_wbs', {id}))
            .then(
                (response) => {
                    commit(types.SET_WBS, {wbs: response.body});
                },
                () => {
                    commit(types.SET_WBS, {wbs: []});
                }
            )
        ;
    },
};

const mutations = {
    [types.SET_WBS](state, {wbs}) {
        state.wbs = wbs;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
