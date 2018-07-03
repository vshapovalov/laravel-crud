import CrudUtils from './utils';

export default class CrudBuilder{

    constructor(crud, crudType){
        this.crud = crud;
        this.crudType = crudType;
    }

    onPick(callback){
        this._onPick = callback;
        return this;
    }

    onGetItem(callback){
        this._onGetItem = callback;
        return this;
    }

    onCancel(callback){
        this._onCancel = callback;
        return this;
    }

    build(){
        return {
            id: CrudUtils.randomInteger(1, 100000),
            name: 'crud-editor',
            options:{
                type: this.type,
                crud: this.crud,
                editorType: this.crudType,
                pickItems: this._onPick,
                close: this._onCancel,
                isModal: this.crudType !== 'browse',
                getItem: this._onGetItem
            }
        };
    }
}