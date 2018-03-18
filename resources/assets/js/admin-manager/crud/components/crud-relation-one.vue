<template>
    <div class="field has-addons">
        <div class="control">
            <input class="input" type="text" placeholder="" :value="relatedName" readonly @dblclick.prevent.stop="openCrud" @click.prevent.stop="showItem">
        </div>
        <div class="control">
            <a class="button is-info" v-show="isButtonVisible('pick')" @click.prevent.stop="openCrud">Выбрать</a>
        </div>
        <div class="control">
            <a class="button is-primary" v-show="isButtonVisible('add')" @click.prevent.stop="addItem">Добавить</a>
        </div>
        <div class="control">
            <a class="button is-warning" v-show="isButtonVisible('edit')" @click.prevent.stop="editItem">Изменить</a>
        </div>
        <div class="control">
            <a class="button is-danger" v-show="isButtonVisible('clear')" @click.prevent.stop="clearItem">Очистить</a>
        </div>
    </div>
</template>

<script>

    import CrudBuilder from './../builder';
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
            showItem(){
                console.log(this.value);
            },

            clearItem(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.changeItem({});
            },
            isButtonVisible(code){
                return !this.field.additional || !this.field.additional.buttons || _.find(this.field.additional.buttons, btn=>btn===code);
            },
            changeItem(item){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                console.log('emit-change', item);

                this.$emit("change", item);
            },
            addItem(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }
                Bus.$emit('editpanel:mount', this.crud, null , this.changeItem);
            },
            editItem(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }
                Bus.$emit('editpanel:mount', this.crud, this.value[this.crud.id] , this.changeItem);
            },
            openCrud(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                if (this.crud)
                {

                    console.log('item from rel-one', this.item);

                    let crudEditor = new CrudBuilder(this.crud, CrudTypes.PICK)
                        .onPick((values)=> {
                            this.changeItem(_.first(values));
                        })
                        .onGetItem(()=>this.item)
                        .build();

                    crudEditor.show();

                } else {
                    toastr.error('CRUD форма не определена по коду');
                }
            }
        },
        beforeMount(){
            this.crud = AdminManager.getCrud(this.field.relation.crud.code);

        },
        mounted() {

        }
    }
</script>
