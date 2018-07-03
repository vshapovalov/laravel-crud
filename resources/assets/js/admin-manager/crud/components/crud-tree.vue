<template>
    <div class="crud-tree"
         @mousemove="onMouseMove"
         @mousedown="onMouseCapture"
         @mouseup="onMouseRelease"
         style="min-height: 100%"
         ref="crudtree"
    >
        <crud-tree-item v-for="item in children"
                        :siblings="children"
                        :key="item[crud.id]"
                        :item="item"
                        :items="items"
                        :crud="crud"
                        @edit="onEditItem"
                        @delete="onDeleteItem"
                        @change="onChange"
                        @select="onSelectItem"
                        @itementer="onItemEnter"
                        @itemleave="onItemLeave"
                        :is-pick-mode="isPickMode"
        ></crud-tree-item>
    </div>
</template>

<script>
    import CrudTypes from './../../utils/types';

    export default {
        name: 'crud-tree',
        props: ['items', 'crud', 'type'] ,
        data: function () {
            return {
                dragDrop:{
                    itemCaptured: false,
                    itemOver: null,
                    item: null,
                    dragged: false
                },
                treeRect: {}
            }
        },
        computed: {
            children(){
                return _.orderBy(_.filter(this.items, (i)=>!i.parent_id), 'order');
            },
            isPickMode(){ return this.type !== CrudTypes.BROWSE; },
        },
        methods: {
            onSelectItem(item){

                if (this.type === CrudTypes.PICK) {

                    _.each(
                        _.filter( this.items, i=>i.isSelected ),
                        (i)=>{ if ( i[this.crud.id] !== item[this.crud.id] ) i.isSelected = false; }
                    );
                }

                item.isSelected = !item.isSelected;

                this.$emit('select', _.filter( this.items, i=>i.isSelected ));
            },
            onItemEnter(item){
                if (this.dragDrop.captured){
                    this.dragDrop.itemOver = item;
                    this.$set( item , 'entered', true);
                }
            },
            onItemLeave(item){
                if (this.dragDrop.captured){
                    this.$set( item , 'entered', false);
                    this.dragDrop.itemOver = null;
                }
            },
            onMouseMove(e){
                if (this.dragDrop.captured){

                    if (!this.dragDrop.dragged) {
                        this.dragDrop.dragged = true;
                        this.$set( this.dragDrop.item , 'captured', true);
                    }

                    this.$set( this.dragDrop.item , 'x', e.screenX);
                    this.$set( this.dragDrop.item , 'y', e.screenY - this.treeRect.top);

                    e.preventDefault();
                }
            },
            onMouseCapture(e){
                let capturedElement = document.elementFromPoint(e.clientX, e.clientY);

                if (capturedElement) {
                    let menuId = capturedElement.getAttribute('data-menu-id');

                    if (menuId){

                        let item = _.find(this.items, i=> i[this.crud.id] == menuId);

                        if (!item.captured){
                            e.stopPropagation();
                            e.preventDefault();
                            this.dragDrop.captured = true;

                            this.dragDrop.item = _.find(this.items, i=> i[this.crud.id] == menuId);

                        }
                    }
                }
            },
            onMouseRelease(){
                if (this.dragDrop.captured){

                    if (this.dragDrop.dragged && this.dragDrop.itemOver){

                        let overItemOrder =
                            this.dragDrop.itemOver.centerPointed ? 0 : this.dragDrop.itemOver.order;
                        let overItemParent =
                            this.dragDrop.itemOver.centerPointed ? this.dragDrop.itemOver.id : this.dragDrop.itemOver.parent_id;

                        _.each(
                            _.orderBy(
                                _.filter(this.items, i=>{
                                    return (i.parent_id === overItemParent)
                                    && (i.order >= overItemOrder);
                                })
                                , 'order'
                            ),
                            (i, ind) =>{ i.order = overItemOrder + (ind + 1)}
                        );

                        this.dragDrop.item.parent_id = overItemParent;
                        this.dragDrop.item.order = overItemOrder;

                        this.dragDrop.itemOver.entered = null;
                        this.dragDrop.itemOver = null;

                        this.$emit('change', _.filter( this.items, i=>i.parent_id == overItemParent ));
                    }

                    this.dragDrop.item.captured = false;
                    this.dragDrop.item.x = 0;
                    this.dragDrop.item.y = 0;
                    this.dragDrop.dragged = false;

                    this.dragDrop.item = null;
                    this.dragDrop.captured = false;
                }
            },

            onEditItem(item){
                this.$emit('edit', item);
            },

            onDeleteItem(item){
                this.$emit('delete', item);
            },

            onChange(items){
                this.$emit("change", items);
            }
        },
        mounted(){
            this.treeRect = this.$refs.crudtree.getBoundingClientRect();
        }
    }
</script>

