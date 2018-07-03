export default {
    install(Vue, options){
        Vue.mixin({
            methods:{
                l18n(code){
                    return App.l18n[code];
                }
            }
        });
    }
}
