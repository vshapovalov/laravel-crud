<template>
    <textarea :id="tinyEditorId" :value="value"></textarea>
</template>

<script>
    import FormBehaviorTypes from '../../utils/types';
    import LibraryBuilder from './../../media/builder';
    import utils from './../utils';

    export default {
        name: 'crud-richedit',
        model:{
            prop: "value",
            event: "change"
        },
        props: ["value", "field"],
        data: function () {
            return {
                tinyEditorId: null
            }
        },
        watch: {
          value(val, oldVal){
              if (oldVal == undefined) tinymce.get(this.tinyEditorId).setContent(val);
          }
        },
        computed: {
            editorSize(){

                let size = (this.field.additional && this.field.additional.size) ? this.field.additional.size : 'medium';

                switch (size) {
                    case 'small':
                        return 300;
                        break;
                    case 'medium':
                        return 500;
                        break;
                    case 'large':
                        return 700;
                        break;
                    default:
                        return +size;
                }
            }

        },
        methods: {
            onChange(content){
                this.$emit("change", content);
            }
        },
        beforeDestroy(){
            tinymce.execCommand('mceRemoveEditor', false, this.tinyEditorId);
        },
        destroy(){

        },
        beforeMount(){
            this.tinyEditorId = 'mce' + utils.randomInteger(100000, 200000);
        },
        mounted() {
            setTimeout(()=>{
                tinymce.init({
                    menubar: false,
                    readonly: false,
                    inline: false,
                    selector: '#' + this.tinyEditorId,
                    min_height: this.editorSize,
                    resize: 'vertical',
                    plugins: 'link, lists, image, code, youtube, giphy, table, textcolor, fullscreen, advlist, colorpicker, contextmenu, paste, visualblocks',
                    extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
                    toolbar: 'styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify| bullist numlist outdent indent | link image table youtube giphy | visualblocks | code | fullscreen',
                    visualblocks_default_state: true,
                    convert_urls: false,
                    image_caption: true,
                    image_title: true,
                    file_picker_callback: function(callback, value, meta) {
                        if (meta.filetype == 'image' || meta.filetype == 'file') {

                            let libraryComponent = new LibraryBuilder(FormBehaviorTypes.PICK).build();

                            libraryComponent.options.closeLibrary = ()=> AdminManager.unmountComponent(libraryComponent) ;
                            libraryComponent.options.pickItems = (items)=>{
                                callback(_.first(items), {});
                            };

                            AdminManager.mountComponent( libraryComponent, false);
                        }

                    },
                    setup:(ed)=>{

                        ed.on('change', (e)=>{
                            this.onChange(ed.getContent());
                        });
                    }
                });
            },0);
        }
    }
</script>
