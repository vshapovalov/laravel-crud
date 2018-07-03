<template>
    <div class="">
        <v-layout row align-center>
            <v-text-field
                    hide-details
                    class="px-0 py-0"
                    @dblclick="showLibrary"
                    readonly
                    :value="message"
                    solo
            ></v-text-field>

            <v-tooltip top>
                <v-btn icon  slot="activator" @click="showLibrary" class="my-0">
                    <v-icon color="success">add</v-icon>
                </v-btn>
                <span>{{ l18n('add') }}</span>
            </v-tooltip>

            <v-tooltip top>
                <v-btn icon  slot="activator" @click="showItemsDialog" class="my-0">
                    <v-icon color="accent">edit</v-icon>
                </v-btn>
                <span>{{ l18n('edit') }}</span>
            </v-tooltip>

            <v-tooltip top>
                <v-btn icon :disabled="items.length == 0" slot="activator" @click="clearItems" class="my-0">
                    <v-icon color="red">clear</v-icon>
                </v-btn>
                <span>{{ l18n('clear') }}</span>
            </v-tooltip>
        </v-layout>

        <v-dialog
                v-model="itemsDialog.active"
                scrollable
                max-width="80%"
        >
            <v-card>
                <v-card-title>
                    <v-layout>
                        <v-subheader>{{ field.caption }}</v-subheader>
                        <v-spacer></v-spacer>
                        <v-tooltip top>
                            <v-btn slot="activator" icon @click="showLibrary" >
                                <v-icon color="success">add</v-icon>
                            </v-btn>
                            <span>{{ l18n('add') }}</span>
                        </v-tooltip>
                    </v-layout>

                </v-card-title>
                <v-divider></v-divider>
                <v-card-text >
                    <v-layout row wrap>
                        <v-flex xs12 :md3="type == 'image'" :key="image" v-for="(image, index) in itemsDialog.items" class="px-1 py-1">
                            <v-card >

                                <v-card-media v-show="type == 'image'":src="fullPath(image)" height="200px"></v-card-media>
                                <v-card-actions>
                                    <v-card-title v-show="type != 'image'"><a :href="fullPath(image)" target="_blank">{{ image }}</a></v-card-title>
                                    <v-spacer></v-spacer>
                                    <v-tooltip top>
                                        <v-btn icon slot="activator"
                                               @click="deleteImage(index)" class="my-0"
                                        >
                                            <v-icon color="red">clear</v-icon>
                                        </v-btn>
                                        <span>{{ l18n('delete') }}</span>
                                    </v-tooltip>
                                </v-card-actions>
                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" @click="submitItemsDialog">
                        {{ l18n('save') }}
                    </v-btn>
                    <v-btn flat color="red" @click="itemsDialog.active = false">
                        {{ l18n('cancel') }}
                    </v-btn>
                </v-card-actions>
            </v-card>

        </v-dialog>
    </div>
</template>

<script>

    import LibraryBuilder from './../../media/builder';
    import FormBehaviorTypes from './../../utils/types';

    export default {
        name: 'crud-image',
        model:{
            prop: "value",
            event: "change"
        },
        props: ['field', 'value'],
        data: function () {
            return {
                items: [],
                message: '',
                itemsDialog:{
                    active: false,
                    items: []
                }
            }
        },
        computed: {
            mode(){
                return (this.field.additional && this.field.additional.mode) ?  this.field.additional.mode : 'multi';
            },
            type(){
                return (this.field.additional && this.field.additional.type) ?  this.field.additional.type : 'file';
            }
        },
        watch: {
            value: {
                handler(val, oldVal){
                    this.parseVal(val);
                }
            },
            items(val, oldVal){
                this.message = val.length + ' ' + ( this.type == 'image' ? l18n('images_qty') : l18n('files_qty'))
            }
        },
        methods: {
            fullPath(item){
                return App.baseUrl + item;
            },

            parseVal(val){
                this.items = [];

                if (!val) return;

                try {
                    this.items.splice(0,0, ...( (this.mode === 'single') ? [val] : JSON.parse(val) ) );
                } catch(e){}

            },

            emitChange(){
                if (this.mode === 'single'){

                    this.$emit('change', (this.items.length>0) ? _.first(this.items) : '' );
                } else {

                    this.$emit('change', JSON.stringify(this.items) );
                }
            },
            onPick(pickedItems){

                let items = this.itemsDialog.active ? this.itemsDialog.items : this.items;

                if (pickedItems && pickedItems.length>0){

                    if (this.mode === 'single'){

                        items = [_.first(pickedItems)];
                    } else {

                        items.splice(items.length, 0, ...pickedItems);
                        items = _.uniq(items);
                    }


                    if (this.itemsDialog.active) {

                        this.itemsDialog.items = items;
                    } else {

                        this.items = items;
                        this.emitChange();
                    }
                }
            },

            showItemsDialog(){

                this.itemsDialog.items.splice(0, this.itemsDialog.items.length, ...this.items );
                this.itemsDialog.active = true;
            },

            submitItemsDialog(){

                this.items.splice(0, this.items.length, ...this.itemsDialog.items);
                this.emitChange();
                this.itemsDialog.active = false
            },

            showLibrary(){

                let libraryComponent = new LibraryBuilder(this.mode === 'multi' ? FormBehaviorTypes.PICK_MANY : FormBehaviorTypes.PICK).build();

                libraryComponent.options.crudField = this.field;
                libraryComponent.options.closeLibrary = ()=> AdminManager.unmountComponent( libraryComponent ) ;

                libraryComponent.options.pickItems = (items)=>{

                    this.onPick(items);
                };

                AdminManager.mountComponent( libraryComponent, false );
            },

            deleteImage(index){

                this.itemsDialog.items.splice(index, 1);
            },

            clearItems(){

                this.items = [];
                this.emitChange();
            }
        },
        beforeMount(){
            this.parseVal(this.value);
        }
    }
</script>
