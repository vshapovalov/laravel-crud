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
              if (oldVal == undefined && val && this.tinyEditorId) tinymce.get(this.tinyEditorId).setContent(val);
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

                    content_css: App.tinymce.content_css ? App.tinymce.content_css : '',
                    style_formats: App.tinymce.style_formats ? App.tinymce.style_formats : [],

                    resize: 'vertical',
                    plugins: 'link, lists, image, code, youtube, giphy, table, textcolor, fullscreen, advlist, colorpicker, contextmenu, paste, visualblocks, hr',
                    extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
                    toolbar: 'styleselect bold italic underline fontsizeselect | forecolor backcolor | hr alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table youtube giphy | visualblocks | code | fullscreen',
                    fontsize_formats: "8px 10px 12px 14px 18px 24px 36px 40px 48px",
                    visualblocks_default_state: true,
                    convert_urls: false,
                    image_caption: true,
                    image_title: true,
                    style_formats_merge: true,

                    file_picker_callback: (callback, value, meta)=>{

                        if (meta.filetype == 'image' || meta.filetype == 'file') {

                            let libraryComponent = new LibraryBuilder(FormBehaviorTypes.PICK).build();

                            let zIndex = tinymce.get(this.tinyEditorId).windowManager.getWindows()[0].$el.context.style.zIndex;

                            tinymce.get(this.tinyEditorId).windowManager.getWindows()[0].$el.context.style.zIndex = 0;

                            libraryComponent.options.closeLibrary = ()=> {
                                AdminManager.unmountComponent(libraryComponent);

                                tinymce.get(this.tinyEditorId).windowManager.getWindows()[0].$el.context.style.zIndex = zIndex;
                            };

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
