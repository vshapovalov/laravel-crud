<template>
    <div class="mt-2 crud-tree-item" :class="{'captured': item.captured }" :style="{left: leftPosition, top: topPosition}" >
        <div :data-drop-menu-id="item[crud.id]"
             @mouseenter="onItemEnter(item)"
             @mouseleave="onItemLeave(item)"
             @mousemove="onMouseMove"
             style="max-width: 50%; "
             :style="{'pointer-events': item.captured ? 'none' : 'auto' }"
             ref="itemcard"
        >
            <div :style="{background: item.entered && !item.centerPointed ? 'red' : 'transparent'}"
                 style="height:3px; margin-bottom: 3px;"
            ></div>
            <v-card style="cursor: pointer">
                <v-card-text  :data-menu-id="item[crud.id]" class="layout align-center pl-1">
                    <!--pointer-events: none;-->
                    <v-icon style="cursor: pointer; pointer-events: none" class="ml-1 mr-1">reorder</v-icon>
                    <div class="">
                        <v-checkbox
                                @click.prevent.stop="onSelectItem(item)"
                                v-show="isPickMode"
                                :value="item.isSelected"
                                hide-details
                        ></v-checkbox>
                    </div>
                    <v-card-title class="py-0 px-0 spacer" style="pointer-events: none"
                    >
                        {{ item[crud.display] }}
                    </v-card-title>
                    <div>
                        <v-tooltip top>
                            <v-btn icon slot="activator" @click="onEditItem(item)" class="mx-0 my-0">
                                <v-icon color="accent">edit</v-icon>
                            </v-btn>
                            <span>{{ l18n('edit') }}</span>
                        </v-tooltip>
                        <v-tooltip top>
                            <v-btn icon slot="activator" @click="onDeleteItem(item)" class="mx-0 my-0">
                                <v-icon color="red">delete</v-icon>
                            </v-btn>
                            <span>{{ l18n('delete') }}</span>
                        </v-tooltip>
                    </div>
                </v-card-text>
            </v-card>
            <div :style="{background: item.entered && item.centerPointed ? 'red' : 'transparent'}"
                 style="background: black; height:3px; margin: 3px 0 3px 33%;"
            ></div>
        </div>
        <crud-tree-item
                v-for="item in children"
                :siblings="children"
                :key="item[crud.id]" :items="items"
                :item="item"
                :crud="crud"
                @select="onSelectItem"
                @edit="onEditItem"
                @delete="onDeleteItem"
                class="ml-3"
                :is-pick-mode="isPickMode"
                @itementer="onItemEnter"
                @itemleave="onItemLeave"
        ></crud-tree-item>
    </div>
</template>

<script>
    import VueCrudTreeItem from './crud-tree-item.vue';
    import CrudUtils from './../utils';


    export default {
        name: 'crud-tree-item',
        props: {
            item:{},
            items:{},
            crud:{},
            siblings:{},
            isPickMode: { default: false }
        },
        data: function () {
            return { }
        },
        components:{
            'crud-tree-item': VueCrudTreeItem
        },
        computed: {
            children(){
                return _.orderBy(_.filter(this.items, i=>i.parent_id === this.item[this.crud.id]), ['order'], ['asc']);
            },
            leftPosition(){ return (this.item.x || 0) + 'px'; },
            topPosition(){ return (this.item.y || 0) + 'px'; },
        },
        methods: {
            onSelectItem(item){
                this.$emit('select', item);
            },
            onMouseMove(e){
                let itemrect = this.$refs.itemcard.getBoundingClientRect();

                this.$set( this.item, 'centerPointed', (e.clientY > itemrect.top + ( itemrect.height / 2 ) ) );
            },
            onItemEnter(item){
                this.$emit('itementer', item)
            },
            onItemLeave(item){
                this.$emit('itemleave', item)
            },
            onEditItem(item){
                this.$emit('edit', item);
            },
            onDeleteItem(item){
                this.$emit('delete', item);
            }
        }
    }
</script>

<style>
    .captured{
        position: fixed;
        width: 700px;
        opacity: 0.7;
        z-index: 200;
        margin: 0;
        pointer-events: none;
    }
</style>