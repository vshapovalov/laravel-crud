
import AdminApi from './api';
import Events from './../events';


export default class AdminManager{

    constructor(managerSelector){

        this.managerSelector = managerSelector;
        this.cruds = {};
        this.managerInstance = null;
        let _this = this;

        new Vue({
            el: this.managerSelector,
            template: '<admin-manager @mount="onMount"></admin-manager>',
            methods: {
                onMount(inst){
                    _this.managerInstance = inst;
                }
            }
        });

        this.requestConfig();
    }

    requestConfig(){

        AdminApi.getConfig()
            .then((resp)=>{

                _.each(resp.data.list, (item)=>{
                    this.cruds[item.code] = item;
                });

                Bus.$emit( Events.ADMIN.CONFIG_LOADED, resp.data );
            })
            .catch((error)=>{
                this.showError(App.l18n['action_error'] + ': ' + error);
            });

    }

    mountComponent(component, unmountActiveComponent = true){
        Bus.$emit( Events.ADMIN.INSTANCE_MOUNT, component, unmountActiveComponent);
    }

    unmountComponent(component){
        Bus.$emit( Events.ADMIN.INSTANCE_UNMOUNT, component);
    }

    showSuccess(text, timeout = 3000){

        this.showMessage(text, 'success', timeout);
    }

    showInfo(text, timeout = 3000){
        this.showMessage(text, 'info', timeout);
    }

    showError(text, timeout = 3000){
        this.showMessage(text, 'error', timeout);
    }

    showMessage(text, type = '', timeout = 0){

        Bus.$emit( Events.ADMIN.SHOW_MESSAGE, { text: text, type: type, timeout: timeout } );
    }

    registerMiddleware(middleware){
        Bus.$emit( Events.ADMIN.MIDDLEWARE_REGISTER, middleware);
    }

    getMiddlewares(){

        return this.managerInstance.middlewares;
    }

    goThroughtMiddlewares(event, options){

        let passedMiddlewareCount = 0;
        let middlewares = this.getMiddlewares();

        _.each( middlewares, m=>{

            let runResult = false;

            m( event, options , (res = true)=> runResult = res );

            if (!runResult) return;

            ++passedMiddlewareCount;
        });

        return (passedMiddlewareCount === middlewares.length);
    }

    getManagerInstance(){
        return this.managerInstance;
    }

    getCrud(crudCode){
        return this.cruds[crudCode];
    }
}

