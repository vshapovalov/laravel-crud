<template>
    <v-layout column fill-height style="position:relative">
        <v-flex class="layout row wrap mb-1">
            <v-tabs class="flex xs12" v-model="activeTab">
                <v-tab :key="0">Tables</v-tab>
                <v-tab :key="1">Crud forms</v-tab>
                <v-tab :key="2">Add form</v-tab>
            </v-tabs>
        </v-flex>
        <v-flex fill-height style="position: relative;">
            <div class="scroll-container">
                <v-tabs-items v-model="activeTab">
                    <v-tab-item :key="0" class="py-2">
                        <v-expansion-panel inset v-model="tablesPanel">
                            <v-expansion-panel-content
                                    v-for="(table, i) in tables"
                                    :key="i"
                            >
                                <div slot="header">{{ table.name }} ({{ table.columns.length }} columns(s))</div>

                                <template v-if="tablesPanel == i">
                                    <v-list >
                                        <v-list-tile :key="column.name" v-for="column in table.columns">
                                            <v-list-tile-content>
                                                <v-list-tile-title>{{ column.name }}</v-list-tile-title>
                                                <v-list-tile-sub-title>type: {{ column.type }}, length: {{ column.length }}, not null: {{ column.not_null }}, default: {{ column.default }}</v-list-tile-sub-title>
                                            </v-list-tile-content>
                                            <v-flex style="flex-grow: 0">
                                                <v-checkbox v-model="column.isSelected"></v-checkbox>
                                            </v-flex>
                                        </v-list-tile>
                                    </v-list>
                                    <div>
                                        <v-btn
                                                flat color="success"
                                                @click="createFromTable( table )"
                                        >make form</v-btn>
                                    </div>
                                </template>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-tab-item>
                    <v-tab-item :key="1" class="py-2">
                        <v-expansion-panel inset v-model="formsPanel">
                            <v-expansion-panel-content
                                    v-for="(form, i)  in crudForms"
                                    :key="i"
                            >
                                <div slot="header">{{ form.name }} ({{ form.fields.length }} fields(s))</div>
                                <template v-if="formsPanel == i">
                                    <v-list >
                                        <v-list-tile :key="field.name" v-for="field in form.fields">
                                            <v-list-tile-content>
                                                <v-list-tile-title>{{ field.name }} ({{ field.caption }})</v-list-tile-title>
                                                <v-list-tile-sub-title>type: {{ field.type }}, validation: {{ field.validation }}, by_default: {{ field.by_default }}</v-list-tile-sub-title>
                                            </v-list-tile-content>
                                            <v-flex style="flex-grow: 0">
                                                <v-checkbox v-model="field.isSelected"></v-checkbox>
                                            </v-flex>
                                        </v-list-tile>
                                    </v-list>
                                    <div>
                                        <v-btn
                                                flat color="success"
                                                @click="createFromForm( form )"
                                        >make form</v-btn>
                                        <v-btn
                                                flat color="accent"
                                                @click="showFormJsonDialog( form )"
                                        >json</v-btn>
                                    </div>
                                </template>

                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-tab-item>
                    <v-tab-item :key="2" class="py-2 px-2">
                        <v-layout>
                            <v-flex xs12 md3>
                                <p class="headline">Form settings</p>
                                <v-text-field v-model="form.name" label="caption"></v-text-field>
                                <v-text-field v-model="form.code" label="code"></v-text-field>
                                <v-text-field v-model="form.model" label="model"></v-text-field>
                                <v-text-field v-model="form.id" label="id field"></v-text-field>
                                <v-text-field v-model="form.display" label="display field"></v-text-field>
                                <v-select v-model="form.type" label="type" :items="formTypes"></v-select>
                                <v-checkbox v-model="createModel" label="Create model"></v-checkbox>
                                <v-btn block flat color="success" @click="saveForm">Save form</v-btn>
                                <v-btn block flat color="accent" @click="showCreateFromJsonDialog">From json</v-btn>
                            </v-flex>
                            <v-flex xs12 md9>
                                <v-tabs v-model="formActiveTab">
                                    <v-tab :key="'fields'">Fields</v-tab>
                                    <v-tab :key="'scopes'">Scopes</v-tab>
                                </v-tabs>
                                <v-tabs-items v-model="formActiveTab" >
                                    <v-tab-item :key="'fields'" class="py-2">
                                        <div class="">
                                            <v-btn flat color="success" @click="addField">Add field</v-btn>
                                        </div>
                                        <v-expansion-panel inset v-model="fieldsPanel">
                                            <v-expansion-panel-content
                                                    v-for="(field, i) in form.fields"
                                                    :key="i"
                                            >
                                                <div slot="header">{{ field.caption }}({{ field.name }})</div>
                                                <template v-if="fieldsPanel == i">
                                                    <v-card>
                                                        <v-card-text class="layout ro wrap">
                                                            <v-flex xs12>
                                                                <v-btn flat @click="deleteField(field)" color="red">Delete</v-btn>
                                                                <v-btn flat @click="setIdField(field)" color="accent">Set as id</v-btn>
                                                                <v-btn flat @click="setDisplayField(field)" color="accent">Set as display</v-btn>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.name" label="name"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.caption" label="caption"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-select v-model="field.type" label="type" :items="columnTypes"></v-select>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-select v-model="field.visibility" label="visibility" :items="visibilityTypes" :multiple="true"></v-select>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.by_default" label="by_default"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.description" label="description"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.tab" label="tab"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 md6 class="px-1 py-1">
                                                                <v-text-field v-model="field.validation" label="validation"></v-text-field>
                                                            </v-flex>
                                                            <v-flex xs12 class="px-1 py-1">
                                                                <v-textarea v-model="field.additional" label="additional"></v-textarea>
                                                            </v-flex>
                                                            <v-flex xs12 md4 class="px-1 py-1">
                                                                <v-select v-model="field.columns" label="columns" :items="columnsWidthTypes" :value-comparator="compareValues"></v-select>
                                                            </v-flex>
                                                            <v-flex xs12 md4 class="px-1 py-1">
                                                                <v-checkbox v-model="field.json" label="json"></v-checkbox>
                                                            </v-flex>
                                                            <v-flex xs12 md4 class="px-1 py-1">
                                                                <v-text-field v-model="field.order" label="order"></v-text-field>
                                                            </v-flex>

                                                        </v-card-text>
                                                    </v-card>
                                                </template>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-tab-item>
                                    <v-tab-item :key="'scopes'" class="py-2">
                                        <div class="">
                                            <v-btn flat color="success" @click="addScope">Add scope</v-btn>
                                        </div>
                                        <v-expansion-panel inset>
                                            <v-expansion-panel-content
                                                    v-for="(scope, i) in form.scopes"
                                                    :key="i"
                                            >
                                                <div slot="header">{{ scope.name }}</div>
                                                <v-card>
                                                    <v-card-text class="layout ro wrap">
                                                        <v-flex xs12>
                                                            <v-btn flat @click="deleteScope(scope)" color="red">Delete</v-btn>
                                                        </v-flex>
                                                        <v-flex xs12 class="px-1 py-1">
                                                            <v-text-field v-model="scope.name" label="name"></v-text-field>
                                                        </v-flex>
                                                    </v-card-text>
                                                </v-card>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-tab-item>
                                </v-tabs-items>
                            </v-flex>
                        </v-layout>

                    </v-tab-item>
                </v-tabs-items>
            </div>
        </v-flex>

        <v-dialog v-model="jsonDialog.active" width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">JSON of form [{{ jsonDialog.formName }}]</span>
                </v-card-title>
                <v-card-text>
                    <v-textarea v-model="jsonDialog.json" height="300px"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn v-show="jsonDialogHasOnSubmit" flat @click="jsonDialogSubmit">Ok</v-btn>
                    <v-btn flat @click="jsonDialog.active = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
    export default {
        name: 'fast-crud',
        props: [ 'options' ],
        data: function () {
            return {
                activeTab: null,
                formActiveTab: null,
                formsPanel: null,
                tablesPanel: null,
                fieldsPanel: null,
                loading: true,
                createModel: false,
                tables: [],
                crudForms: [],
                form: {
                    fields: [],
                    scopes: []
                },
                formTypes: ['list', 'tree'],
                columnTypes: [ 'checkbox', 'colorbox', 'datepicker', 'dropdown', 'image', 'textarea', 'textbox', 'richedit' ],
                visibilityTypes: [ 'browse', 'add', 'edit', 'hidden'],
                columnsWidthTypes : [ 12, 6, 4, 3 ],
                jsonDialog:{
                    formName: null,
                    json: null,
                    active: false,
                    onSubmit: null
                }
            }
        },
        computed:{
            jsonDialogHasOnSubmit(){
                return this.jsonDialog.onSubmit;
            }
        },

        methods:{
            clearForm(){

                this.form = {
                    fields: [],
                    scopes: []
                };
            },
            saveForm(){
                axios
                    .post(App.baseUrl + '/' +  App.crudPrefix + '/extra/forms', { form: this.form, createModel: this.createModel })
                    .then((res)=>{
                        AdminManager.showInfo('Form created');
                        this.clearForm;
                    })
                    .catch((err)=>{
                        AdminManager.showError(err);
                    });
            },

            createFormFromJson(){
                let form = JSON.parse( this.jsonDialog.json );

                this.createFromForm( form );
            },
            jsonDialogSubmit(){
                if (this.jsonDialog.onSubmit)
                    this.jsonDialog.onSubmit();
            },
            showCreateFromJsonDialog(){
                this.jsonDialog.formName = 'Paste json';
                this.jsonDialog.json = null;
                this.jsonDialog.onSubmit = ()=>{
                    this.createFormFromJson();
                    this.jsonDialog.active = false;
                };

                this.jsonDialog.active = true;
            },
            addField(){
                this.form.fields.splice(0, 0, { caption: 'new field', name: 'new field' } );
            },
            addScope(){
                this.form.scopes.splice(0, 0, { name: 'new scope', code: 'new scope' } );
            },
            showFormJsonDialog( form ){
                this.jsonDialog.formName = form.name;
                this.jsonDialog.json = JSON.stringify( form );
                this.jsonDialog.onSubmit = null;
                this.jsonDialog.active = true;
            },
            deleteField(field){
                this.form.fields = _.filter(this.form.fields, f=>f.name!=field.name);
            },
            deleteScope(scope){
                this.form.scopes = _.filter(this.form.scope, s=>s.name!=scope.name);
            },
            setIdField(field){
                this.form.id = field.name;
            },
            setDisplayField(field){
                this.form.display = field.name;
            },
            createFromForm( form ){

                this.form = JSON.parse( JSON.stringify(form) );

                let fields = _.filter( this.form.fields, f=>f.isSelected);

                if (fields.length) this.form.fields = fields;

                _.each( this.form.fields, f=>{
                    f.visibility = f.visibility ? JSON.parse(f.visibility) : [];
                });

                this.activeTab = 2;
            },
            createFromTable( table ){

                let hasParentId = false;
                let hasOrder = false;

                this.form = {
                    code: table.name,
                    name: table.name,
                    fields: [],
                    id: 'id',
                    display: 'title',
                    model: table.model,
                    scopes: []
                };

                let columns = _.filter(table.columns, c=>c.isSelected);

                let fields = _.map( columns.length ? columns : table.columns, (c,i)=> {

                    if (c.name == 'parent_id') hasParentId = true;
                    if (c.name == 'order') hasOrder = true;

                    return {
                        name: c.name,
                        caption: c.name,
                        type: (
                        c.type == 'timestamp'
                        || c.type == 'date'
                        || c.type == 'date') ? 'datepicker' : 'textbox',
                        order: i,
                        columns: 6,
                        description: null,
                        validation: c.not_null ? 'required' : null,
                        visibility: [ "browse", "add", "edit"],
                        by_default: c.default,
                        json: 0,
                        readonly: 0,
                        tab: 'Основные параметры',
                        additional: ''
                    };
                });

                this.form.fields.splice( 0, this.form.fields.length, ...fields);

                this.form.type = (hasParentId && hasOrder) ? 'tree' : 'list';

                this.activeTab = 2;

            },
            compareValues(a,b){
                return (a == b);
            }
        },
        mounted(){
            axios
                .get(App.baseUrl + '/' +  App.crudPrefix + '/extra/tables')
                .then((res)=>{
                    this.tables.splice(0, 0, ...res.data )
                })
                .catch(err=>{
                    AdminManager.showError( err );
                });
            axios
                .get(App.baseUrl + '/' +  App.crudPrefix + '/extra/forms')
                .then((res)=>{
                    this.crudForms.splice(0, 0, ...res.data )
                })
                .catch(err=>{
                    AdminManager.showError( err );
                });
        }
    }
</script>
