<template>
    <table class="table crud-table">
        <thead>
        <tr>
            <th data-role="button" v-for="field in crudBrowsableFields"
                @click.stop="sort(field)"><i
                    class="fa" :class="{'fa-arrow-up': sorting[field.name] ==='asc', 'fa-arrow-down': sorting[field.name] ==='desc'}"></i>{{ field.caption }}</th>
            <!--<th> Действия</th>-->
            <th v-if="rowActions.indexOf('edit') >= 0" style="width: 30px; text-align:center;"><i class="fa fa-pencil"></i></th>
            <th v-if="rowActions.indexOf('delete') >= 0" style="width: 30px; text-align:center;"><i class="fa fa-trash"></i></th>
        </tr>
        </thead>
        <tbody>
        <tr v-show="items && items.length === 0">
            <td>Нет записей</td>
        </tr>

        <tr v-show="items && items.length">
            <td v-for="field in crudBrowsableFields">
                <input v-if="isFilterVisible(field)" type="text" class="input" :field-name="field.name"
                       @keyup.stop.prevent="filter" placeholder="Поиск">
            </td>
        </tr>


        <tr v-for="item in preparedItems" @click.prevent="selectItem(item)" :class="{'is-selected': item.isSelected}">
            <td v-for="field in crudBrowsableFields">
                <crud-caption :item="item" :field="field" :is-pivot="field.isPivot"></crud-caption>
            </td>
            <td v-if="rowActions.indexOf('edit') >= 0">
                <a  class="button is-warning is-outlined"
                    @click.prevent.stop="editItem(item)"><i class="fa fa-pencil"></i></a>
            </td>
            <td v-if="rowActions.indexOf('delete') >= 0">
                <a  class="button is-danger is-outlined"
                   @click.prevent.stop="deleteItem(item)"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        </tbody>
    </table>
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
                'default': ()=>['edit', 'delete']
            },
            pivotFields: { default : ()=>[]},
        },
        data: function () {
            return {
                sorting: {},
                filtering: {},

                filterHelper: {
                    fields: {},
                    debouncedFilter: null
                }
            }
        },
        computed: {
            preparedItems(){

                let items = [];

                let filterFields = _.keys(this.filtering);

                if (filterFields.length) {

                    items = _.filter(this.items, (i)=>{

                        return _.every(filterFields, (ff)=>{

                            return i[ff] && (_.lowerCase(i[ff]).indexOf(this.filtering[ff]) >=0);
                        });
                    });

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
                // TODO: we need field display value resolver, for using filter fo all field types

                return (field.type === FieldTypes.TEXTAREA) || (field.type === FieldTypes.TEXTBOX)
                    || (field.type === FieldTypes.COLORBOX) || (field.type === FieldTypes.DATEPICKER);
            },

            filter(e){
                if (e.target.value.length) {
                    this.filterHelper.fields[e.target.getAttribute('field-name')] = e.target.value;
                } else {
                    delete this.filterHelper.fields[e.target.getAttribute('field-name')];
                }

                this.filterHelper.debouncedFilter();
            },

            applyFilter(){


                this.filtering = _.clone(this.filterHelper.fields);

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
            this.filterHelper.debouncedFilter = _.debounce( this.applyFilter, 1000);
        },
        mounted() {

        }
    }
</script>

