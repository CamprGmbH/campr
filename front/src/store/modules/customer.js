import Vue from 'vue';
import * as types from '../mutation-types';

const state = {
    items: [],
};

const getters = {
    customers: state => state.items,
    customersForFilter: function(state) {
        let customersForFilter = [{'key': '', 'label': 'All Customers'}];
        state.items.map(function(customer) {
            customersForFilter.push({'key': customer.id, 'label': customer.name});
        });

        return customersForFilter;
    },
};

const actions = {
    /**
     * Get all customers.
     * @param {function} commit
     */
    getCustomers({commit}) {
        Vue.http
            .get(Routing.generate('app_api_company_list').substr(1)).then((response) => {
                if (response.status === 200) {
                    let customers = response.data;
                    commit(types.SET_CUSTOMERS, {customers});
                }
            }, (response) => {
                // TODO: REMOVE MOCK DATA
                let customers = [
                    {
                        'id': 1,
                        'name': 'Company1',
                    },
                    {
                        'id': 2,
                        'name': 'Comapny2',
                    },
                    {
                        'id': 3,
                        'name': 'Company3',
                    },
                ];
                commit(types.SET_CUSTOMERS, {customers});
            });
    },
};

const mutations = {
    /**
     * Sets customers to state
     * @param {Object} state
     * @param {array} customers
     */
    [types.SET_CUSTOMERS](state, {customers}) {
        state.items = customers;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
