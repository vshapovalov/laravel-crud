<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card" style="height: 100%; width: 100%; margin: 20px;">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ editState === editStates.ADD ? 'Создание' : 'Редактирование' }}</p>
                <button class="delete" aria-label="close" @click="doCancel"></button>
            </header>
            <section class="modal-card-body">
                <div  class="edit-form">
                    <div class="tabs">
                        <ul>
                            <li v-for="tab in crudTabs" :class="{'is-active' : activeTab == tab}"><a @click="setActiveTab(tab)">{{ tab }}</a></li>
                        </ul>
                    </div>

                    <div v-for="tab in crudTabs" v-show="activeTab == tab">
                        <div class="field " v-for="field in crudEditableFields" v-if="field.tab == tab || (!field.tab & tab == 'основные параметры')">
                            <label class="label">{{ field.caption }}<small v-show="field.description"> ({{field.description }})</small></label>

                            <div class="control">

                                <crud-textbox v-if="getFieldType(field) === fieldTypes.TEXTBOX" v-model="item[field.name]" :field="field" @change="onSlugify(field)"></crud-textbox>
                                <crud-checkbox v-if="getFieldType(field) === fieldTypes.CHECKBOX" v-model="item[field.name]" :field="field"></crud-checkbox>
                                <crud-textarea v-if="getFieldType(field) === fieldTypes.TEXTAREA" v-model="item[field.name]" :field="field"></crud-textarea>
                                <crud-richedit v-if="getFieldType(field) === fieldTypes.RICHEDIT" v-model="item[field.name]" :field="field"></crud-richedit>
                                <crud-datepicker v-if="getFieldType(field) === fieldTypes.DATEPICKER" v-model="item[field.name]" :field="field"></crud-datepicker>
                                <crud-colorbox v-if="getFieldType(field) === fieldTypes.COLORBOX" v-model="item[field.name]" :field="field"></crud-colorbox>
                                <crud-image v-if="getFieldType(field) === fieldTypes.IMAGE" v-model="item[field.name]" :field="field"></crud-image>
                                <crud-dropdown v-else-if="getFieldType(field) === fieldTypes.DROPDOWN" v-model="item[field.name]"
                                        :field="field"></crud-dropdown>
                                <crud-relation-one
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO || field.relation.type === relationTypes.HAS_ONE))"
                                        v-model="item[toSnake(field.relation.name)]" :crud-code="field.relation.crud" :field="field" :item="item"></crud-relation-one>
                                <crud-relation-many
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && field.relation.type == relationTypes.HAS_MANY)"
                                        v-model="item[toSnake(field.relation.name)]" :crud-code="field.relation.crud" :field="field" :item="item"></crud-relation-many>
                                <crud-relation-many
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && field.relation.type == relationTypes.BELONGS_TO_MANY)"
                                        v-model="item[toSnake(field.relation.name)]" :crud-code="field.relation.crud" :field="field" :item="item"></crud-relation-many>
                            </div>
                        </div>
                    </div>
                    <div class="field "></div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <a href="#" class="button is-success" @click.prevent.stop="doApprove">Сохранить</a>
                <a href="#" class="button is-danger is-outlined" @click.prevent.stop="doCancel">Отмена</a>
            </footer>
        </div>
    </div>
</template>

<script>
    import RelationTypes from './../utils/relation-types';
    import FieldTypes from './../utils/field-types';
    import VisibilityTypes from './../utils/visibility-types';
    import EditStates from './../utils/states';

    export default {
        name: 'crud-editpanel',
        model: {
            prop: 'item',
            event: 'change'
        },
        props: ['item', 'fields', 'crud'],
        data: function () {
            return {
                activeTab: '',

            }
        },
        computed: {
            editState(){
                return this.item[this.crud.id] ? EditStates.EDIT : EditStates.ADD;
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

                return _.filter( this.fields, (f)=> f.visibility.indexOf(visibilityFilter) >= 0 );

            },
            crudTabs(){
                return _.keys(_.groupBy(this.fields, (f)=> f.tab ? f.tab : "основные параметры"));
            },
            fieldTypes(){ return FieldTypes; },
            relationTypes(){ return RelationTypes; },
        },

        methods: {
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
                        console.log('jpathvalue', tmpValue);
                    });

                    value = tmpValue;
                }

                return value;
            },
            getFieldType(field){
                let result = '';

                if (field.type === FieldTypes.DYNAMIC){

                    if (field.dynamic && field.dynamic.type === 'relation') {

                        let [fieldName, relationField] = field.dynamic.from.split('.');

                        result = this.item[_.snakeCase(fieldName)][relationField];

                    } else {

                        if (field.dynamic && field.dynamic.type === 'field') {

                            result = this.item[field.dynamic.from];
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
            /****************************** tabs *************************************/

            setActiveTab(tabName){
                this.activeTab = tabName;
            },

            /****************************** end tabs *************************************/

            doApprove(){
                this.$emit('change', this.item);
                this.$emit('approve');
            },
            doCancel(){
                this.$emit('cancel');
            },
        },
        mounted() {
            this.activeTab = this.crudTabs[0];


        }
    }
</script>