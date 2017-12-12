<template>
    <div class="field has-addons">
        <div class="control">
            <input class="input" type="text" placeholder="" :value="relatedName" readonly @dblclick.prevent.stop="openCrud">
        </div>
        <div class="control">
            <a class="button is-info" @click.prevent.stop="openCrud">
                Выбрать
            </a>

        </div>
        <div class="control">
            <a class="button is-danger" @click.prevent.stop="clearItem">
                Очистить
            </a>
        </div>
    </div>
</template>

<script>

    import CrudBuilder from './../builder';
    import CrudTypes from './../../utils/types';

    export default {
        name: 'crud-relation-one',
        model: {
            prop: "selectedItem",
            event: "change"
        },
        props: {
            crudCode: {
                type: String
            },
            selectedItem: {
                type: Object
            },
            field: {},
            item: {}
        },
        data: function () {
            return {
                crud: {}
                ,
            }

        },
        computed: {
            relatedName(){
                return (this.selectedItem) ? this.selectedItem[this.crud.display] : '';
            }
        },
        methods: {

            clearItem(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.changeItem({});
            },
            changeItem(item){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", item);
            },
            openCrud(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                if (this.crud)
                {

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
            this.crud = AdminManager.getCrud(this.crudCode)
        },
        mounted() {

        }
    }
</script>
