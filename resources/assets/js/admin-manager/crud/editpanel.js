import VueCrudEditpanel from './components/crud-editpanel.vue';

export default class CrudEditPanel {
    constructor(crudSelector, crud, editorType, onPick, onCancel){

        this.crud = crud;
        this.crudSelector = crudSelector;
        this.editorType = editorType;
        this.onPick = onPick;
        this.onCancel = onCancel;
        this.inputItem = onGetItem ? onGetItem() : undefined;
    }

    createEditor(){
        let _this = this;

        this.editor =  new Vue({
            template: '<crud-editpanel id="' + _this.crudSelector + '" :active="active" :crud="crud" :editor-type="editorType" @pick="onPick" @cancel="onCancel" :item="item"></crud-editpanel>',
            data: {
                crud: _this.crud,
                editorType: _this.editorType,
                active: true,
                item: _this.inputItem
            },
            components: {
                'crud-editpanel': VueCrudEditpanel
            },
            watch:{
                active: {
                    handler: function(val, oldVal){
                        if (!val) {
                            this.closeCrud();
                        }
                    }
                }
            },
            methods: {
                closeCrud(){
                    this.$nextTick(()=>{
                        this.$destroy();
                    });
                },
                onPick(value){
                    if (_this.onPick) { _this.onPick(value); }
                    this.active = false;
                },
                onCancel(){
                    if (_this.onCancel) { _this.onCancel(); }
                    this.active = false;
                },

            }
        });
    }

    getData(){
        return this.editor.data;
    }

    show(elementId){

        if (elementId){
            this.crudSelector = elementId;
        }


        this.createEditor();

        this.editor.$mount('#'+ this.crudSelector);

        return this.editor;
    }

    close(){
        this.editor.active = false;
    }
}
