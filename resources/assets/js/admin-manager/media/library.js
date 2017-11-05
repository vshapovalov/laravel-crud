import VueMediaLibrary from './components/media-library.vue';

export default class MediaLibrary {
    constructor(selector, type, onPick, onCancel){
        this.selector = selector;
        this.type = type;
        this.onPick = onPick;
        this.onCancel = onCancel;

        let _this = this;

        this.library =  new Vue({
            template: '<media-library id="' + _this.selector + '" :active="active" :type="type" @pick="onPick" @cancel="onCancel"></media-library>',
            data: {
                type: _this.type,
                active: true
            },
            watch: {
                active: {
                    handler(val, oldVal){
                        if (!val) {
                            this.closeLibrary();
                        }
                    }
                }
            },
            components: {
                'media-library': VueMediaLibrary
            },
            methods: {
                closeLibrary(){

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

                }
            }
        });
    }

    show(){
        this.library.$mount('#'+this.selector);

        return this.library;
    }

    close(){
        this.library.active = false;
    }
}
