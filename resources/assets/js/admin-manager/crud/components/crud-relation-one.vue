<template>
    <v-layout row align-center>
        <v-text-field
                hide-details
                class="px-0 py-0"
                @dblclick="openCrud"
                readonly
                :value="relatedName"
                solo
        ></v-text-field>

        <v-tooltip v-show="isButtonVisible('pick')" top>
            <v-btn icon slot="activator" @click="openCrud" class="my-0">
                <v-icon>search</v-icon>
            </v-btn>
            <span>{{ l18n('pick') }}</span>
        </v-tooltip>

        <v-tooltip v-show="isButtonVisible('add')" top>
            <v-btn icon  slot="activator" @click="addItem" class="my-0">
                <v-icon color="success">add</v-icon>
            </v-btn>
            <span>{{ l18n('add') }}</span>
        </v-tooltip>

        <v-tooltip v-show="isButtonVisible('edit')" top>
            <v-btn icon :disabled="!value" slot="activator" @click="editItem" class="my-0">
                <v-icon color="accent">edit</v-icon>
            </v-btn>
            <span>{{ l18n('edit') }}</span>
        </v-tooltip>

        <v-tooltip v-show="isButtonVisible('clear')" top>
            <v-btn icon :disabled="!value" slot="activator" @click="clearItem" class="my-0">
                <v-icon color="red">clear</v-icon>
            </v-btn>
            <span>{{ l18n('clear') }}</span>
        </v-tooltip>
    </v-layout>
</template>

<script>

    import CrudBuilder from './../builder';
    import EditPanel from './../editpanel';

    import CrudTypes from './../../utils/types';
    import Utils from './../utils';

    export default {
        name: 'crud-relation-one',
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
                crud: {}
            }

        },
        computed: {
            relatedName(){
                return (this.value) ? Utils.getDisplayValue(this.crud.display, this.value) : '';
            }
        },
        methods: {
            clearItem(){

                this.changeItem( null );
            },
            isButtonVisible(code){
                return !this.field.additional || !this.field.additional.buttons || _.find(this.field.additional.buttons, btn=>btn===code);
            },
            changeItem(item){

                this.$emit("change", item);
            },
            addItem(){
                let component = new EditPanel(this.crud, null, null).build();

                component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                component.options.save = (item)=> {

                    this.changeItem(item);
                    AdminManager.unmountComponent( component );
                } ;

                component.options.isModal = true;

                AdminManager.mountComponent( component, false );
            },
            editItem(){

                let component = new EditPanel(this.crud, this.value[this.crud.id], null).build();

                component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                component.options.save = (item)=> {

                    this.changeItem(item);
                    AdminManager.unmountComponent( component );
                } ;

                component.options.isModal = true;

                AdminManager.mountComponent( component, false );
            },
            openCrud(){

                if (this.crud) {

                    let component = new CrudBuilder( this.crud, CrudTypes.PICK ).build();

                    component.options.pickItems = (items)=> {

                        this.changeItem(_.first(items));
                    };
                    component.options.getItem = ()=> this.item ;
                    component.options.close = ()=> AdminManager.unmountComponent( component ) ;
                    component.options.isModal = true;

                    AdminManager.mountComponent( component, false );

                } else {
                    AdminManager.showError( this.l18n('crud_form_not_found') );
                }
            }
        },
        beforeMount(){

            this.crud = AdminManager.getCrud(this.field.relation.crud.code);
        }
    }
</script>
