import CrudUtils from './utils';

export default class CrudEditPanel {
    constructor(crud, itemId, onSave, onCancel){

        this.crud = crud;
        this.onSave = onSave;
        this.itemId = itemId;
        this.onCancel = onCancel;
    }

    build(){
        return {
            id: CrudUtils.randomInteger(1, 100000),
            name: 'crud-editpanel',
            options: {
                crud: this.crud,
                save: this.onSave,
                close: this.onCancel,
                itemId: this.itemId
            }
        }
    }
}
