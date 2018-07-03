export default {
    ADMIN: {
        INSTANCE_MOUNT: 'admin:instance:mount',
        INSTANCE_UNMOUNT_NAME: 'admin:instance:unmount:name',
        INSTANCE_UNMOUNT: 'admin:instance:unmount',
        MIDDLEWARE_REGISTER: 'admin:middleware:register',
        URL_RENDER: 'admin:url:render',
        URL_GO: 'admin:url:go',
        CONFIG_LOADED: 'admin:config:loaded',
        SHOW_MESSAGE: 'admin:show-message'
    },
    EDIT_PANEL: {
        MOUNT: 'editpanel:mount',
        ON_MOUNT: 'editpanel:on:mount',
        BEFORE_SAVE: 'editpanel:before:save',
        FETCH: 'editpanel:on:fetch'
    },
    MEDIA_LIBRARY: {
        MOUNT: 'medialibrary:mount'
    },
    CRUD_EDITOR: {
        MOUNT: 'crud:on:mount',
        FETCH: 'crud:on:fetch',
        EDIT: 'crud:on:edit',
        ADD: 'crud:on:add',
        DELETE: 'crud:on:delete'
    }
};
