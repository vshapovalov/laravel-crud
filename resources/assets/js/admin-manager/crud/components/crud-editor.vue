<template>
    <div class="crud-editor fill-height" >
        <v-layout column fill-height>
            <v-flex>
                <v-toolbar v-if="options.isModal" card flat dense color="primary">
                    <v-btn icon @click="pickCancel">
                        <v-icon>clear</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ crud.name }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn color="success" @click="pickRows" :disabled="loading">{{ l18n('pick') }}</v-btn>
                </v-toolbar>

                <h3 v-if="!options.isModal" class="headline">{{ crud.name }}</h3>

                <div>
                    <v-tooltip bottom>
                        <v-btn slot="activator" :disabled="loading" @click.prevent="getList" flat icon><v-icon>refresh</v-icon></v-btn>
                        <span>{{ l18n('refresh') }}</span>
                    </v-tooltip>
                    <v-btn flat :disabled="loading" @click.prevent="addItem" color="success">{{ l18n('add') }}</v-btn>
                </div>

                <div :key="component.id" :is="component.name" :options="component.options" v-for="component in userComponents"></div>

                <v-text-field
                        v-if="crud.type === 'list'"
                        prepend-icon="search"
                        :label="l18n('search_label')"
                        v-model.trim="filterValue"
                        autofocus
                        class="mt-3"
                        clearable
                ></v-text-field>
            </v-flex>
            <v-flex fill-height style="position: relative;">
                <div class="scroll-container">
                    <crud-table v-if="crud.type === 'list'"
                                :items="items"
                                :crud="crud" :type="editorType"
                                @select="selectItems" @edit="editItem" @delete="deleteItem"
                                :loading="loading"
                                :filter="filterValue"
                    ></crud-table>
                    <crud-tree v-else-if="crud.type === 'tree'"
                               :items="items" :crud="crud"
                               :type="editorType"
                               :loading="loading"
                               @select="selectItems"
                               @edit="editItem" @delete="deleteItem" @change="onTreeChange"
                    ></crud-tree>
                </div>
            </v-flex>
        </v-layout>

        <v-dialog v-model="approveDialog.active" max-width="500px">
            <v-card>
                <v-card-title>{{ approveDialog.title }}</v-card-title>
                <v-card-text>{{ approveDialog.text }}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" flat @click="submitApproveDialog">{{ l18n('approve') }}</v-btn>
                    <v-btn color="red" flat outlined @click="cancelApproveDialog">{{ l18n('cancel') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import States from './../utils/states';
    import CrudTypes from './../../utils/types';
    import EditPanel from './../editpanel';
    import CrudUtils from './../utils';
    import CrudApi from './../../api';
    import Events from './../../../events';

    export default {
        name: 'crud-editor',
        props: {
            options:{
                type: Object
            }
        },
        data: function () {
            return {
                items: [],
                state: States.BROWSE,
                selectedItems: [],
                approveDialog: {
                    title: '',
                    text: '',
                    active: false,
                    onApprove: undefined
                },
                loading: false,
                filterValue: '',
                userComponents: []
            }
        },

        computed: {
            editorTypes(){ return CrudTypes; },
            isPickMode(){ return this.editorType !== CrudTypes.BROWSE; },
            crud(){ return this.options.crud },
            editorType(){ return this.options.editorType },
            item(){ return this.options.item }
        },
        methods: {

            showApproveDialog(title, text, onApprove){

                this.approveDialog.title = title;
                this.approveDialog.text = text;
                this.approveDialog.onApprove = onApprove;
                this.approveDialog.active = true;
            },

            submitApproveDialog(){

                if (this.approveDialog.onApprove)
                    this.approveDialog.onApprove();

                this.cancelApproveDialog();
            },

            cancelApproveDialog(){
                this.approveDialog.onApprove = undefined;
                this.approveDialog.active = false;
            },

            pickRows(){

                this.options.pickItems( this.selectedItems );
                this.options.close();
            },

            pickCancel(){

                this.options.close();
            },

            onTreeChange(items){

                CrudApi.crudBulkUpdate(this.crud.code, { items: items })
                    .then((response)=>{
                        AdminManager.showInfo(this.l18n('success_completed'));
                    })
                    .catch((error)=>{
                        AdminManager.showError(this.l18n('action_error') + ': ' + error);
                    });
            },

            addItem(){

                let middlewaresResult = AdminManager.goThroughtMiddlewares(
                    Events.CRUD_EDITOR.ADD,
                    { crud: this.crud, editorType: this.editorType, item: this.item, items: this.items }
                );

                if (!middlewaresResult) return;

                let component = new EditPanel(this.crud, null, null, null).build();

                component.options.isModal = true;
                component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                component.options.save = ()=> {

                    AdminManager.unmountComponent( component );

                    this.getList()
                };

                AdminManager.mountComponent(component, false);
            },
            editItem(item){

                let middlewaresResult = AdminManager.goThroughtMiddlewares(
                    Events.CRUD_EDITOR.EDIT,
                    { crud: this.crud, editorType: this.editorType, item: this.item, items: this.items }
                );

                if (!middlewaresResult) return;

                let component = new EditPanel(this.crud, item[this.crud.id], null, null).build();

                component.options.isModal = true;
                component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                component.options.save = ()=> {

                    AdminManager.unmountComponent( component );

                    this.getList()
                };

                AdminManager.mountComponent(component, false);
            },


            deleteItem(item){

                let middlewaresResult = AdminManager.goThroughtMiddlewares(
                    Events.CRUD_EDITOR.DELETE,
                    { crud: this.crud, editorType: this.editorType, item: this.item, items: this.items }
                );

                if (!middlewaresResult) return;

                this.showApproveDialog(this.l18n('delete_action'), this.l18n('want_delete'),()=>{

                    this.loading = true;

                    CrudApi.crudDeleteItem(this.crud.code, item[this.crud.id])
                        .then((response)=>{

                            if (response.data.status == 'success'){

                                let rowIndex = _.findIndex(this.items, (r)=>{ return (r[this.crud.id] === item[this.crud.id]) });

                                this.items.splice(rowIndex, 1);

                                AdminManager.showSuccess(this.l18n('success_delete'));
                            } else {

                                AdminManager.showError(this.l18n('action_error') + ': ' + response.data.error);
                            }

                            this.loading = false;
                        })
                        .catch((error)=>{

                            this.loading = false;

                            AdminManager.showError(this.l18n('action_error') + ': ' + error);
                        });
                });
            },

            selectItems(items){
                this.selectedItems = items;
            },

            getList(){

                this.loading = true;

                CrudApi.crudGetItems(this.crud.code, { item: this.item })
                    .then((response)=>{

                        if (response.data.status === 'success'){

                            this.items = _.map ( response.data.items , i=>{

                                this.$set(i, 'isSelected', false);

                                return CrudUtils.spreadJsonFields(i, this.crud.fields, false);
                            });

                            AdminManager.goThroughtMiddlewares(
                                Events.CRUD_EDITOR.FETCH,
                                { crud: this.crud, editorType: this.editorType, item: this.item, items: this.items }
                            );

                        } else {

                            if (response.data.status === 'error'){

                                if (response.data.errors) {
                                    _.each(response.data.errors, (f)=>{
                                        _.each(f, (m)=>{
                                            AdminManager.showError(m);
                                        });
                                    });

                                } else {
                                    AdminManager.showError( this.l18n('action_error') );
                                }
                            }
                        }

                        this.loading = false;
                    })
                    .catch((error)=>{

                        this.loading = false;

                        AdminManager.showError( this.l18n('action_error') + ': ' + error);
                    });
            },
            addUserComponent(components){
                this.userComponents.splice(this.userComponents.length,0,...components);
            },
        },
        beforeMount(){

            AdminManager.goThroughtMiddlewares(
                Events.CRUD_EDITOR.MOUNT,
                {
                    crud: this.crud,
                    editorType: this.editorType,
                    item: this.item,
                    addComponents: this.addUserComponent
                }
            );
        },
        mounted() {
            this.getList();
        }
    }
</script>