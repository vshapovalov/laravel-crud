import apiUrls from './urls';

export default class CrudApi {

    static getUrlByCrud(code){
        return apiUrls.crud.BASE + code;
    }

    static crudList(){

        return axios.get(apiUrls.crud.BASE + apiUrls.crud.LIST);
    }

    static getConfig(){

        return axios.get(apiUrls.crud.BASE + apiUrls.config);
    }

    static menuList(){

        return axios.get(apiUrls.crud.BASE + apiUrls.menu.LIST);
    }

    static crudBulkUpdate(code,data){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.BULK_UPDATE, data);
    }

    static crudGetItem(code, id){

        return axios.get(this.getUrlByCrud(code) + '/' + id);
    }

    static crudGetItems(code, data){
        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.GET_ITEMS, data);
    }

    static crudSaveItem(code, data){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.SAVE_ITEM, data);
    }

    static crudDeleteItem(code, id){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.DELETE_ITEM + id);
    }

    static mediaFolderNew(data){
        return axios.post(apiUrls.media.FOLDER_NEW, data);
    }

    static mediaFolderRename(data){
        return axios.post(apiUrls.media.FOLDER_RENAME, data);
    }

    static mediaItemsDelete(data){
        return axios.post(apiUrls.media.ITEMS_DELETE, data);
    }
    static mediaGetItems(params){
        return axios.post(apiUrls.media.GET_ITEMS, params);
    }

    static mediaItemsMove(params){
        return axios.post(apiUrls.media.MOVE_ITEMS, params);
    }

    static mediaItemCrop(params){
        return axios.post(apiUrls.media.CROP, params);
    }

}

