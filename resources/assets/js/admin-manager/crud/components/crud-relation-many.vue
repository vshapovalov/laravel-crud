<template>
    <div class="">
        <div >
            <v-btn flat color="primary" @click="pickItems" v-show="isButtonVIsible('pick')" >{{ l18n('pick') }}</v-btn>
            <v-btn flat color="success" @click="addItem" v-show="isButtonVIsible('add')">{{ l18n('add') }}</v-btn>
            <v-btn flat color="red" :disabled="!items || items.length === 0" @click="delAllItems" v-show="isButtonVIsible('delete_all')">{{ l18n('delete_all') }}</v-btn>
        </div>
        <crud-table :items="items" :type="crudTypes.BROWSE" :crud="crud" @delete="deleteItem" @edit="editItem"
                    :row-actions="tableActions" :pivot-fields="field.relation.pivot"></crud-table>

        <v-dialog
                v-model="editDialog.active"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-layout column fill-height>
                <v-flex>
                    <v-toolbar color="primary" flat dense extended >
                        <v-btn icon @click="cancelEditDialog">
                            <v-icon>clear</v-icon>
                        </v-btn>
                        <v-toolbar-title>{{ l18n('edit') }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn color="success" @click="submitEditDialog">{{ l18n('save') }}</v-btn>

                        <v-tabs
                                color="primary"
                                slot="extension"
                                v-model="activeTab"
                        >
                            <v-tab v-for="tab in pivotTabs" :key="tab">
                                {{ tab }}
                            </v-tab>
                        </v-tabs>
                    </v-toolbar>
                </v-flex>

                <v-flex style="position: relative" fill-height>
                    <div class="scroll-container v-card v-card--flat">
                        <v-card flat>
                            <v-tabs-items v-model="activeTab">
                                <v-tab-item v-for="tab in pivotTabs" :key="tab" class="py-2 px-2 layout wrap">
                                    <div
                                            class="flex py-1 px-1 xs12"
                                            :class="[getFieldWidth(field)]"
                                            v-for="field in pivotFields"
                                            v-if="field.tab == tab || (!field.tab & tab == l18n('common_params'))"
                                    >
                                        <v-subheader class="pl-0 pb-1">{{ field.caption }}<small v-show="field.description"> ({{field.description }})</small></v-subheader>
                                        <crud-textbox v-if="field.type === fieldTypes.TEXTBOX" v-model="pivotRow[field.name]" :field="field"></crud-textbox>
                                        <crud-checkbox v-if="field.type === fieldTypes.CHECKBOX" v-model="pivotRow[field.name]" :field="field"></crud-checkbox>
                                        <crud-textarea v-if="field.type === fieldTypes.TEXTAREA" v-model="pivotRow[field.name]" :field="field"></crud-textarea>
                                        <crud-datepicker v-if="field.type === fieldTypes.DATEPICKER" v-model="pivotRow[field.name]" :field="field"></crud-datepicker>
                                        <crud-colorbox v-if="field.type === fieldTypes.COLORBOX" v-model="pivotRow[field.name]" :field="field"></crud-colorbox>
                                        <crud-image v-if="field.type === fieldTypes.IMAGE" v-model="pivotRow[field.name]" :field="field"></crud-image>
                                        <crud-dropdown v-if="field.type === fieldTypes.DROPDOWN" v-model="pivotRow[field.name]" :field="field"></crud-dropdown>
                                        <!--<crud-relation-one-->
                                        <!--v-else-if="(field.type === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO ))"-->
                                        <!--v-model="pivotRow[field.name]" :crud-code="field.relation.crud" :field="field" :item="pivotRow"></crud-relation-one>-->
                                    </div>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-card>
                    </div>
                </v-flex>
            </v-layout>
        </v-dialog>
    </div>
</template>

<script>
    import CrudUtils from './../utils';
    import EditPanel from './../editpanel';
    import CrudBuilder from './../builder';
    import CrudTypes from './../../utils/types';
    import FieldTypes from './../utils/field-types';
    import RelationTypes from './../utils/relation-types';

    export default {
        name: 'crud-relation-many',
        model: {
            prop: "items",
            event: "change"
        },
        props: {
            items: { type: Array },
            field: {
                type: Object
            },
            item: {}
        },
        data: function () {
            return {
                crud: {},
                activeTab: '',
                pivotRow: {},
                editDialog: {
                    active : false,
                    onSuccess: null
                },
            }
        },
        computed: {
            relationTypes(){
                return RelationTypes;
            },

            crudTypes(){
                return CrudTypes;
            },
            pivotFields(){
                return _.filter(this.field.relation.pivot, (f)=> f.visibility && _.some(f.visibility,(v)=>v==='edit'));
            },
            tableActions(){

                let actions = ['delete', 'edit'];

                return actions;
            },
            pivotTabs(){
                return _.keys(_.groupBy(this.pivotFields, (f)=> f.tab ? f.tab : this.l18n('common_params') ));
            },
            fieldTypes(){
                return FieldTypes;
            },
            buttons(){
                return this.field.additional && this.field.additional.buttons ? this.field.additional.buttons : ['add' ,'pick', 'delete_all']
            }
        },
        methods: {
            getFieldWidth(field){
                return 'md' + field.columns;
            },

            isButtonVIsible(btn){
                return _.find(this.buttons, b=>b===btn );
            },

            showEditDialog(onSuccess){
                this.editDialog.onSuccess = onSuccess;
                this.editDialog.active = true;
                this.activeTab = 0;
            },
            submitEditDialog(){
                if (this.editDialog.onSuccess) this.editDialog.onSuccess();
                this.cancelEditDialog();
            },
            cancelEditDialog(){
                this.editDialog.onSuccess = null;
                this.editDialog.active = false;
            },

            addItem(){

                let component = new EditPanel(this.crud, null, null).build();

                component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                component.options.save = (item)=> {

                    this.saveItem(item);
                    AdminManager.unmountComponent( component );
                } ;

                component.options.isModal = true;

                AdminManager.mountComponent( component, false );
            },

            saveItem(item){

               this.mergedItems([item]);
            },

            delAllItems(){
                this.emitChanges([]);
            },

            deleteItem(item){
                let items = _.filter(this.items, (i) => i[this.crud.id] !== item[this.crud.id]);

                this.emitChanges(items);
            },
            editItem(item){
                if (this.field.relation.pivot.length){

                    this.pivotRow = item.pivot;

                    this.showEditDialog();
                } else {

                    let component = new EditPanel(
                        this.crud,
                        item[this.crud.id],
                        (item)=>{
                            this.saveItem(item);
                            AdminManager.unmountComponent( component )
                        }).build();

                    component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                    component.options.isModal = true;

                    AdminManager.mountComponent( component, false );
                }

            },
            emitChanges(items){
                this.$emit("change", items);
            },
            pickItems(){
                if (this.crud)
                {

                    let component = new CrudBuilder( this.crud, CrudTypes.PICK_MANY ).build();

                    component.options.pickItems = (items)=> { this.mergedItems(items); };
                    component.options.item = this.item;
                    component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                    component.options.isModal = true;

                    AdminManager.mountComponent( component, false );

                } else {
                    AdminManager.showError( this.l18n('crud_form_not_found') );
                }
            },
            mergedItems(pickedItems){

                if(pickedItems && pickedItems.length > 0){

                    _.each(pickedItems, (i)=>{
                        if (i.isSelected){
                            i.isSelected = false;
                        }

                        if (this.field.relation.pivot){

                            let sameItem = _.find(this.items, (ti)=>ti[this.crud.id] === i[this.crud.id]);

                            if (sameItem) {
                                this.$set(i, 'pivot', sameItem.pivot);
                            } else {
                                let emptyPivot = CrudUtils.createMetaObject(this.field.relation.pivot);
                                this.$set(i, 'pivot', emptyPivot);
                            }
                        }

                    });

                    if (this.items) pickedItems.splice(pickedItems.length, 0, ...this.items);

                    pickedItems = _.uniqBy(pickedItems, this.crud.id);

                    this.emitChanges(pickedItems);

                }
            }
        },
        beforeMount(){

            this.crud = AdminManager.getCrud(this.field.relation.crud.code);
        }
    }
</script>
