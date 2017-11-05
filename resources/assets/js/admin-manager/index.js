
import AdminApi from './api';

export default class AdminManager{
    constructor(managerSelector, workspaceSelector){

        this.managerSelector = managerSelector;
        this.workspaceSelector = workspaceSelector ? workspaceSelector : '#workspace';
        this.cruds = [];
        let _this = this;

        this.managerInstance = new Vue({
            el: this.managerSelector,
            template: "<admin-manager :cruds='cruds'></admin-manager>",
            data: {
                cruds: _this.cruds
            }
        });

        this.requestCruds();

    }

    requestCruds(){

        AdminApi.crudList()
            .then((resp)=>{
                this.cruds = resp.data;
                this.managerInstance.cruds = this.cruds;
            })
            .catch((error)=>{
                toastr.error(error, 'Не удалось получить список CRUD!');
            });

    }

    getManagerInstance(){
        return this.managerInstance;
    }

    getWorkspaceSelector(){
        return this.workspaceSelector;
    }

    getCrud(crudCode){

        return this.cruds[crudCode];

    }

}

