require("babel-polyfill");

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

window.CSRF_TOKEN = token.content;

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import each from 'lodash/each';
import sortBy from 'lodash/sortBy';
import filter from 'lodash/filter';
import findIndex from 'lodash/findIndex';
import first from 'lodash/first';
import snakeCase from 'lodash/snakeCase';
import find from 'lodash/find';
import map from 'lodash/map';
import cloneDeep from 'lodash/cloneDeep';
import includes from 'lodash/includes';
import isString from 'lodash/isString';
import keys from 'lodash/keys';
import groupBy from 'lodash/groupBy';
import uniqBy from 'lodash/uniqBy';
import uniq from 'lodash/uniq';
import some from 'lodash/some';
import lowerCase from 'lodash/lowerCase';
import has from 'lodash/has';
import get from 'lodash/get';
import split from 'lodash/split';
import endsWith from 'lodash/endsWith';
import orderBy from 'lodash/orderBy';
import values from 'lodash/values';

window._ = {
    each, sortBy, filter, findIndex, first, snakeCase,
    find, map, cloneDeep, isString, keys, groupBy, uniqBy, some,
    lowerCase, values, has, get, split, endsWith, orderBy, uniq,
    includes
};

window.dropzone = require('dropzone');

import { slugify } from 'transliteration';

window.slugify = slugify;

window.Vue = require('vue');

import Vuetify from 'vuetify';
import L18n from './l18n';

Vue.use(L18n);

Vue.use(Vuetify, {
    theme: App.theme.colors
});


window.Bus = new Vue({});

Vue.component('admin-manager', require('./admin-manager/components/admin-manager.vue'));
Vue.component('crud-table', require('./admin-manager/crud/components/crud-table.vue'));
Vue.component('crud-editpanel', require('./admin-manager/crud/components/crud-editpanel.vue'));
Vue.component('crud-tree', require('./admin-manager/crud/components/crud-tree.vue'));
Vue.component('crud-textbox', require('./admin-manager/crud/components/crud-textbox.vue'));
Vue.component('crud-checkbox', require('./admin-manager/crud/components/crud-checkbox.vue'));
Vue.component('crud-textarea', require('./admin-manager/crud/components/crud-textarea.vue'));
Vue.component('crud-colorbox', require('./admin-manager/crud/components/crud-colorbox.vue'));
Vue.component('crud-datepicker', require('./admin-manager/crud/components/crud-datepicker.vue'));
Vue.component('crud-richedit', require('./admin-manager/crud/components/crud-richedit.vue'));
Vue.component('crud-dropdown', require('./admin-manager/crud/components/crud-dropdown.vue'));
Vue.component('crud-relation-dropdown', require('./admin-manager/crud/components/crud-relation-dropdown.vue'));
Vue.component('crud-relation-one', require('./admin-manager/crud/components/crud-relation-one.vue'));
Vue.component('crud-relation-many', require('./admin-manager/crud/components/crud-relation-many.vue'));
Vue.component('crud-image', require('./admin-manager/crud/components/crud-image.vue'));
Vue.component('crud-tree-item', require('./admin-manager/crud/components/crud-tree-item.vue'));
Vue.component('crud-caption', require('./admin-manager/crud/components/crud-caption.vue'));
Vue.component('crud-editor', require('./admin-manager/crud/components/crud-editor.vue'));
Vue.component('media-library', require('./admin-manager/media/components/media-library.vue'));


import AdminManager from './admin-manager';

function ready(fn) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

ready( ()=> {

    window.AdminManager = new AdminManager('#admin-manager');

});

