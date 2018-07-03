<template>
    <div style="overflow-x:  auto;">
        <table class="v-datatable v-table">
            <thead>
            <tr>
                <th v-show="isPickMode" style="width: 30px; text-align:center;" class="px-0">
                    <v-menu offset-y>
                        <v-btn slot="activator" icon><v-icon>more_vert</v-icon></v-btn>
                        <v-list>
                            <v-list-tile @click="changePreparedSelected(true)">
                                <v-list-tile-avatar>
                                    <v-icon>check_circle</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-action>{{ l18n('select_all') }}</v-list-tile-action>
                            </v-list-tile>
                            <v-list-tile @click="changePreparedSelected(false)">
                                <v-list-tile-avatar>
                                    <v-icon>check_circle_outline</v-icon>
                                </v-list-tile-avatar>
                                <v-list-tile-title>{{ l18n('deselect_all') }}</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </th>

                <th data-role="button" v-for="field in crudBrowsableFields"
                    @click.stop="sort(field)"
                    class="column sortable"
                    :class="{'active asc': sorting[field.name] ==='asc', 'active desc': sorting[field.name] ==='desc'}"
                >
                    {{ field.caption }}
                    <v-icon style="font-size: 16px;">arrow_upward</v-icon>
                </th>
                <th v-if="rowActions.indexOf('edit') >= 0" style="width: 30px; text-align:center;" class="px-0">
                    <v-icon >edit</v-icon>
                </th>
                <th v-if="rowActions.indexOf('delete') >= 0" style="width: 30px; text-align:center;" class="px-0">
                    <v-icon >delete</v-icon>
                </th>
            </tr>
            </thead>
            <tbody>

            <tr v-show="items && (items.length === 0) && !loading">
                <td></td><td>{{ l18n('list_empty') }}</td>
            </tr>

            <tr
                    v-for="item in preparedItems"
                    @dblclick.prevent.stop="editItem(item)"
                    @click.prevent.stop="selectItem(item)"
                    :active="item.isSelected"
                    style="cursor: pointer;"
            >
                <td v-show="isPickMode">
                    <v-checkbox v-model="item.isSelected" hide-details></v-checkbox>
                </td>
                <td v-for="field in crudBrowsableFields">
                    <crud-caption :item="item" :field="field" :is-pivot="field.isPivot"></crud-caption>
                </td>

                <td v-if="rowActions.indexOf('edit') >= 0"  class="px-0">
                    <v-btn icon :disabled="loading" @click="editItem(item)" class="mx-0">
                        <v-icon color="accent">edit</v-icon>
                    </v-btn>
                </td>
                <td v-if="rowActions.indexOf('delete') >= 0"  class="px-0">
                    <v-btn icon :disabled="loading" @click="deleteItem(item)" class="mx-0">
                        <v-icon color="red">delete</v-icon>
                    </v-btn>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import States from './../utils/states';
    import CrudTypes from './../../utils/types';
    import CrudUtils from './../utils';
    import RowWrapper from './../utils/row-wrapper';
    import FieldTypes from './../utils/field-types';
    import RelationTypes from './../utils/relation-types';
    import VisibilityTypes from './../utils/visibility-types';

    export default {
        name: 'crud-table',
        props: {
            crud: { type: Object },
            type: { type: String },
            items: { type: Array },
            rowActions: {
                type: Array,
                'default': ()=>[ 'edit', 'delete' ]
            },
            loading: { type: Boolean },
            pivotFields: { default : ()=>[] },
            filter: { default: '' }
        },
        data: function () {
            return {
                sorting: {},
            }
        },
        computed: {
            filterableFields(){
                return _.filter( this.crudBrowsableFields, f=> this.isFilterVisible(f) );
            },

            preparedItems(){

                let items = [];

                if (this.filter && (this.filter !== '')) {

                    items = _.filter(this.items, i=>

                         _.some( this.filterableFields,
                                f=>{ return _.lowerCase(i[ f.name ]).indexOf(this.filter) >= 0; }
                            )
                    );

                } else {
                    items = this.items;
                }

                let sortFields = _.keys(this.sorting);

                if (sortFields.length){
                    let sortDirection = _.values(this.sorting);

                    items = _.orderBy(items, sortFields, sortDirection );
                }

                return items;
            },
            crudBrowsableFields(){

                let fields = _.filter(
                    this.crud.fields, (f)=> (_.findIndex(f.visibility, (v) => v === VisibilityTypes.BROWSE ) >= 0)
                );

                if (this.pivotFields.length) {
                    fields.splice(fields.length, 0, ..._.map(_.filter(this.pivotFields, (pv)=>_.some(pv.visibility,(v)=>v===VisibilityTypes.BROWSE)),(f)=>{
                        f.isPivot = true;
                        return f;
                    }));
                }

                return fields;
            },
            editorTypes(){ return CrudTypes; },
            isPickMode(){ return this.type !== CrudTypes.BROWSE; },
            fieldTypes(){ return FieldTypes; },
            relationTypes(){ return RelationTypes; }
        },
        methods: {
            isFilterVisible(field){
                // TODO: we need field value resolver, for using filter fo all field types

                return (field.type === FieldTypes.TEXTAREA) || (field.type === FieldTypes.TEXTBOX)
                    || (field.type === FieldTypes.COLORBOX) || (field.type === FieldTypes.DATEPICKER);
            },

            changePreparedSelected(isSelected){

                _.each(this.preparedItems, (i)=>{
                    i.isSelected = isSelected;
                });
            },

            selectItem(item){

                if (this.isPickMode){

                    if (this.type === CrudTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i[this.crud.id] !== item[this.crud.id])
                                this.$set(i, 'isSelected', false);
                        });
                    }

                    this.$set(item, 'isSelected', !item.isSelected);

                    this.$emit("select", _.filter(this.items, (i) => i.isSelected ));
                }
            },

            editItem(item){

                this.$emit('edit', item);
            },

            deleteItem(item){

                this.$emit('delete', item);
            },

            sort(field){

                if (!_.has(this.sorting, field.name)) {
                    this.$set(this.sorting, field.name, 'asc');
                } else {
                    if (this.sorting[field.name] === 'desc') {
                        this.$delete(this.sorting , field.name);
                    } else {
                        this.sorting[field.name] = 'desc';
                    }
                }
            }
        },
        beforeMount(){

        },
        mounted() {

        }
    }
</script>

<style lang="scss">
    .v-datatable {
        td, th{
            word-wrap: break-word;
            word-break: break-all;
            padding: 0 2px !important;
        }
    }
</style>