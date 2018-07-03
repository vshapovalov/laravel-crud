<template>
    <v-select
            :items="items"
            :item-value="relationCrud.id"
            :item-text="relationCrud.display"
            v-model="selected"
            :multiple="!isSingleMode"
            max-height="400"
            class="pt-0"
            solo
            clearable
            :loading="loading"
            :value-comparator="compareId"
    ></v-select>
</template>

<script>

    import CrudTypes from './../../utils/types';
    import Utils from './../utils';
    import CrudApi from './../../api';
    import RelationTypes from './../utils/relation-types';

    export default {
        name: 'crud-relation-dropdown',
        model: {
            prop: "value",
            event: "change"
        },
        props: {
            field: {},
            item: null,
            value: null
        },
        data: function () {
            return {
                relationCrud: {},
                items: [],
                loading: false,
                selected: null
            }

        },
        watch:{
            value(val, oldVal){
                if (oldVal === undefined && val)
                    this.selected = this.isSingleMode ? val[this.relationCrud.id] : _.map( val, v=>v[this.relationCrud.id]);
            },
            selected(val, oldVal){
                this.emitChanges();
            }
        },
        computed: {
            isSingleMode(){
                return (this.field.relation.type === RelationTypes.BELONGS_TO || this.field.relation.type === RelationTypes.HAS_ONE);
            },
            itemIds(){
                return _.map(this.items, i=>i[this.relationCrud.id]);
            }
        },
        methods: {
            compareId(a,b){
                return a==b;
            },
            clearItem(){
                this.changeItem( null );
            },
            emitChanges(){

                this.$emit("change",
                    this.isSingleMode
                        ? _.find( this.items, i=> i[this.relationCrud.id] == this.selected)
                        : _.filter( this.items , i=> _.includes( this.selected, i[this.relationCrud.id] ) )
                );
            },
        },
        mounted(){
            this.loading = true;

            this.relationCrud = AdminManager.getCrud(this.field.relation.crud.code);
            CrudApi.crudGetItems(this.relationCrud.code, { item: this.item })
                .then((response)=>{

                    this.items = _.map ( response.data.items , i=>{

                        this.$set(i, 'isSelected', false);

                        return Utils.spreadJsonFields(i, this.relationCrud.fields, false);
                    });

                    this.loading = false;
                })
                .catch((error)=>{

                    this.loading = false;

                    AdminManager.showError( this.l18n('action_error') + ': ' + error);
                });
        }
    }
</script>
