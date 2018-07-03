export default {
    config: 'config',
    crud: {
        BASE: App.baseUrl + '/' + App.crudPrefix + '/',
        LIST: 'list',
        BULK_UPDATE: '/bulk/update',
        SAVE_ITEM: '/edit',
        DELETE_ITEM: '/delete/',
        GET_ITEMS: '/items',
    },
    menu:{
        LIST: 'menu/list'
    },
    media: {
        BASE: App.baseUrl + '/' + App.mediaPrefix + '/',
        FOLDER_NEW: App.baseUrl + '/' + App.mediaPrefix + '/folder/new',
        FOLDER_RENAME: App.baseUrl + '/' + App.mediaPrefix + '/folder/rename',
        ITEMS_DELETE: App.baseUrl + '/' + App.mediaPrefix + '/items/delete',
        GET_ITEMS: App.baseUrl + '/' + App.mediaPrefix + '/items',
        UPLOAD: App.baseUrl + '/' + App.mediaPrefix + '/upload',
        MOVE_ITEMS: App.baseUrl + '/' + App.mediaPrefix + '/items/move',
        CROP: App.baseUrl + '/' + App.mediaPrefix + '/crop'
    }
};