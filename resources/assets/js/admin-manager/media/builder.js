import Utils from './../crud/utils';

export default class MediaLibraryBuilder{

    constructor(type){

        this.type = type;
        this.crudField = null;
        this.pickCallback = null;
        this.cancelCallback = null;
    }

    onPick(pickCallback){

        this.pickCallback = pickCallback;
        return this;
    }

    setCrudField(crudField){

        this.crudField = crudField;
        return this;
    }

    onCancel(cancelCallback){

        this.cancelCallback = cancelCallback;
        return this;
    }

    build(){

        return {
            id: 'component' + Utils.randomInteger(1, 100000),
            name: 'media-library',
            options:{
                type: this.type,
                crudField: this.crudField,
                pickItems: this.pickCallback,
                closeLibrary: this.cancelCallback,
                isModal: this.type !== 'browse'
            }
        }
    }

}
