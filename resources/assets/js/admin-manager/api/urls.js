export default {
    crud: {
        BASE: baseUrl + '/' + crudPrefix + '/',
        LIST: 'list',
        TREE_MOVE: '/tree/move',
        SAVE_ITEM: '/edit',
        DELETE_ITEM: '/delete/',
        GET_ITEMS: '/items',
    },
    media: {
        BASE: baseUrl + '/' + mediaPrefix + '/',
        FOLDER_NEW: baseUrl + '/' + mediaPrefix + '/folder/new',
        FOLDER_RENAME: baseUrl + '/' + mediaPrefix + '/folder/rename',
        ITEMS_DELETE: baseUrl + '/' + mediaPrefix + '/items/delete',
        GET_ITEMS: baseUrl + '/' + mediaPrefix + '/items',
        UPLOAD: baseUrl + '/' + mediaPrefix + '/upload',
    }
};