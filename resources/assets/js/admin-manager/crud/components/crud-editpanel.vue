<template>
    <v-layout column fill-height>
        <v-flex>
            <v-toolbar card flat dense color="primary" extended>
                <v-btn icon @click="doCancel">
                    <v-icon>clear</v-icon>
                </v-btn>
                <v-toolbar-title>{{ editState === editStates.ADD ? l18n('create') : l18n('edit') }} [{{ crud.name }}]</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="doSave" :disabled="!isReady">{{ l18n('save') }}</v-btn>

                <div slot="extension" class="flex xs12">
                    <div  :key="component.id" :is="component.name" :options="component.options" :crud="crud" :item="item" v-for="component in userComponents"></div>

                    <v-tabs v-model="activeTab" color="primary">
                        <v-tab v-for="tab in crudTabs" :key="tab">
                            {{ tab }}
                        </v-tab>
                    </v-tabs>
                </div>
            </v-toolbar>
        </v-flex>

        <v-flex fill-height style="position: relative">
            <div class="scroll-container ">
                <v-tabs-items v-model="activeTab">
                    <v-tab-item v-for="tab in crudTabs" :key="tab" class="py-2 px-2 layout wrap">
                        <div
                                class="flex py-1 px-1 xs12"
                                :class="[getFieldWidth(field)]"
                                v-for="field in crudEditableFields"
                                v-if="field.tab == tab || (!field.tab & tab == l18n('common_params'))"
                        >
                            <v-subheader class="pl-0 pb-1">{{ field.caption }}<small v-show="field.description"> ({{field.description }})</small></v-subheader>

                            <crud-textbox v-if="getFieldType(field) === fieldTypes.TEXTBOX" v-model="item[field.name]" :field="field" @change="onSlugify(field)"></crud-textbox>
                            <crud-checkbox v-if="getFieldType(field) === fieldTypes.CHECKBOX" v-model="item[field.name]" :field="field"></crud-checkbox>
                            <crud-textarea v-if="getFieldType(field) === fieldTypes.TEXTAREA" v-model="item[field.name]" :field="field"></crud-textarea>
                            <crud-richedit v-if="getFieldType(field) === fieldTypes.RICHEDIT" v-model="item[field.name]" :field="field"></crud-richedit>
                            <crud-datepicker v-if="getFieldType(field) === fieldTypes.DATEPICKER" v-model="item[field.name]" :field="field"></crud-datepicker>
                            <crud-colorbox v-if="getFieldType(field) === fieldTypes.COLORBOX" v-model="item[field.name]" :field="field"></crud-colorbox>
                            <crud-image v-if="getFieldType(field) === fieldTypes.IMAGE" v-model="item[field.name]" :field="field"></crud-image>
                            <crud-dropdown v-else-if="getFieldType(field) === fieldTypes.DROPDOWN" v-model="item[field.name]"
                                           :field="field"></crud-dropdown>
                            <crud-relation-dropdown
                                    v-else-if="(getFieldType(field) === fieldTypes.RELATION && (field.additional && field.additional.mode == 'simple'))"
                                    v-model="item[field.json ? field.name : toSnake(field.name)]" :field="field" :item="item"></crud-relation-dropdown>
                            <crud-relation-one
                                    v-else-if="(getFieldType(field) === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO || field.relation.type === relationTypes.HAS_ONE))"
                                    v-model="item[field.json ? field.name : toSnake(field.name)]" :field="field" :item="item"></crud-relation-one>
                            <crud-relation-many
                                    v-else-if="(getFieldType(field) === fieldTypes.RELATION && (field.relation.type == relationTypes.HAS_MANY || field.relation.type == relationTypes.BELONGS_TO_MANY))"
                                    v-model="item[toSnake(field.name)]" :field="field" :item="item"></crud-relation-many>
                        </div>
                    </v-tab-item>
                </v-tabs-items>
            </div>
        </v-flex>
    </v-layout>
</template>

<script>
    import RelationTypes from './../utils/relation-types';
    import FieldTypes from './../utils/field-types';
    import VisibilityTypes from './../utils/visibility-types';
    import EditStates from './../utils/states';
    import CrudUtils from './../utils';
    import CrudApi from './../../api';
    import Events from './../../../events';


    export default {
        name: 'crud-editpanel',
        props: [ 'options' ],
        data: function () {
            return {
                activeTab: '',
                item: {},
                isReady: false,
                userComponents: []
            }
        },
        computed: {

            crud(){ return this.options.crud; },
            itemId(){ return this.options.itemId; },

            editState(){
                return this.itemId ? EditStates.EDIT : EditStates.ADD;
            },
            editStates(){
                return EditStates;
            },
            crudEditableFields(){

                let visibilityFilter;

                switch (this.editState){
                    case EditStates.ADD:{
                        visibilityFilter = VisibilityTypes.ADD;
                        break;
                    }
                    case EditStates.EDIT:{
                        visibilityFilter = VisibilityTypes.EDIT;
                        break;
                    }
                    default:{
                        visibilityFilter = VisibilityTypes.BROWSE;
                    }
                }

                return _.filter( this.crud.fields, (f)=> f.visibility.indexOf(visibilityFilter) >= 0 );

            },
            crudTabs(){
                return _.keys(_.groupBy(this.crud.fields, (f)=> f.tab ? f.tab : this.l18n('common_params') ) );
            },
            fieldTypes(){ return FieldTypes; },
            relationTypes(){ return RelationTypes; },
        },

        methods: {
            getFieldWidth(field){
                return 'md' + field.columns;
            },

            onSlugify(field){
                if(field.additional && field.additional.slugify){
                    this.item[field.additional.slugify] = slugify(this.item[field.name]).toLowerCase();
                }
            },

            getFieldValue(field){
                let value = this.item[field.name];

                if (field.json) {
                    let jPath = field.name.split('->');

                    let tmpValue = this.item[jPath[0]];

                    jPath.splice(0,1);

                    _.each(jPath,(p)=>{
                        tmpValue = tmpValue[p];
                    });

                    value = tmpValue;
                }

                return value;
            },
            getFieldType(field){
                let result = '';

                if (field.type === FieldTypes.DYNAMIC){

                    if (field.additional && field.additional.type === 'related') {

                        let [fieldName, relationField] = field.additional.from.split('.');

                        result = this.item[_.snakeCase(fieldName)][relationField];

                    } else {

                        if (field.additional && field.additional.type === 'field') {

                            result = this.item[field.additional.from];
                        }
                    }
                } else {
                    result = field.type;
                }

                return result;
            },

            toSnake(val){
                return _.snakeCase(val);
            },

            doSave(){
                this.isReady = false;

                let middlewaresResult = AdminManager.goThroughtMiddlewares(
                    Events.EDIT_PANEL.BEFORE_SAVE,
                    { crud: this.crud, item: this.item }
                );

                if (!middlewaresResult) return;

                Bus.$emit('edit-panel:before-save', );

                CrudApi.crudSaveItem(this.crud.code, { item: this.item})
                    .then((response)=>{

                        if (response.data.status === 'success'){

                            this.options.save( response.data.item );

                            AdminManager.showSuccess( this.l18n('success_completed') );
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

                        this.isReady = true;
                    })
                    .catch((error)=>{
                        AdminManager.showError(this.l18n('action_error') + ': ' + error);
                        this.isReady = true;
                    });
            },
            doCancel(){
                this.options.close();
            },
            addUserComponent(components){
                this.userComponents.splice(this.userComponents.length,0,...components);
            },
        },
        beforeMount() {
            this.activeTab = 0;

            AdminManager.goThroughtMiddlewares(
                Events.EDIT_PANEL.ON_MOUNT,
                {crud: this.crud, addComponents: this.addUserComponent}
            );

            if (!this.itemId) {
                this.item = CrudUtils.createMetaObject(this.crud.fields);
                CrudUtils.spreadJsonFields(this.item, this.crud.fields, true);
                this.isReady = true;
            } else {
                CrudApi.crudGetItem(this.crud.code, this.itemId)
                    .then((response) => {
                        CrudUtils.spreadJsonFields(response.data, this.crud.fields, true);
                        this.item = response.data;

                        AdminManager.goThroughtMiddlewares(
                            Events.EDIT_PANEL.FETCH,
                            {crud: this.crud, item: this.item}
                        );

                        this.isReady = true;
                    })
                    .catch((error) => {
                        AdminManager.showError(this.l18n('action_error') + ': ' + error);
                        console.error('editpanel:getitem', error);
                        this.isReady = true;
                    });
            }
        }
    }
</script>