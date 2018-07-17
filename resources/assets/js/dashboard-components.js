Vue.component('fast-crud', require('./dashboard-components/fast-crud.vue'));

let fastCrud = {
    id: 'fast-crud',
    name: 'fast-crud',
    options: {
        isModal: false,
        counterStartValue: 100
    }
};

Bus.$on('user:fast-crud:mount', ()=> AdminManager.mountComponent( fastCrud , true) );