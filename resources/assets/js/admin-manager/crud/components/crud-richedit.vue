<template>
    <textarea id="richedit" class="textarea" ref="tiny" :value="value" @change="onChange"></textarea>
</template>

<script>
    let tinymce = require('tinymce');

    import FormBehaviorTypes from '../../utils/types';
    import LibraryBuilder from './../../media/builder';

    export default {
        name: 'crud-richedit',
        model:{
            prop: "value",
            event: "change"
        },
        props: ["value", "field"],
        data: function () {
            return { }
        },
        watch: {
          value(val, oldVal){
              if (oldVal == undefined) tinymce.activeEditor.setContent(val);
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
            tinymce.remove();
        },
        mounted() {
            tinymce.init({
                menubar: false,
                target: this.$refs.tiny,
                min_height: this.editorSize,
                resize: 'vertical',
                plugins: 'link, lists, image, code, youtube, giphy, table, textcolor, fullscreen, advlist, colorpicker, contextmenu, paste, visualblocks',
                extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
                toolbar: 'styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table youtube giphy | visualblocks | code | fullscreen',
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
        }
    }
</script>
