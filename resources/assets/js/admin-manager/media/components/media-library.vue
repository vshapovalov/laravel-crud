<template>
    <div class="media-library fill-height"
         :style="{'z-index': options.isModal ? 100000 : 'inherite'}"
    >
        <v-layout column fill-height>
            <v-flex>
                <v-toolbar v-if="options.isModal" card flat dense color="primary">
                    <v-btn icon @click="closeLibrary">
                        <v-icon>clear</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ title }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn v-if="isPickFolderMode" color="success" @click="moveItem">
                        {{ l18n('move_here') }}
                    </v-btn>
                    <v-btn color="success" v-if="isPickMode && !isPickFolderMode"
                           :disabled="(pickedItems.length === 0) || !isLibraryAvailable" @click="pickItems"
                    >
                        {{ l18n('pick') }}
                    </v-btn>
                </v-toolbar>

                <h3 v-if="!options.isModal" class="headline">{{ title }}</h3>

                <v-layout row wrap >
                    <v-btn id="upload"
                           v-show="!isPickFolderMode"
                           :disabled="!isLibraryAvailable"
                           color="primary"
                           @click="uploadTrigger"
                    >
                        <v-icon class="pr-1">cloud_upload</v-icon>
                        {{ l18n('upload') }}
                    </v-btn>
                    <div ref="uploadPreview" style="display: none"></div>

                    <v-btn flat color="success" :disabled="!isLibraryAvailable" @click="newFolder">
                        {{ l18n('create_folder') }}
                    </v-btn>

                    <v-btn flat color="red" :disabled="!isLibraryAvailable || (pickedItems.length == 0)" @click="deleteItems(null)">
                        {{ l18n('delete') }}
                    </v-btn>

                    <v-spacer></v-spacer>

                    <v-btn v-if="!isPickFolderMode" flat :disabled="(pickedItems.length == 0) || !isLibraryAvailable" @click="moveItem">
                        {{ l18n('move') }}
                    </v-btn>
                </v-layout>


                <v-text-field
                        prepend-icon="search"
                        :label="l18n('search_label')"
                        v-model.trim="search.value"
                        autofocus
                        class="mt-2"
                        clearable
                ></v-text-field>

                <v-progress-linear v-show="uploadProgress.active" :indeterminate="true"></v-progress-linear>

                <v-breadcrumbs class="pl-4 pt-0">
                    <v-icon slot="divider">forward</v-icon>
                    <v-breadcrumbs-item
                            v-for="(pathItem, index) in pathItems" @click.native="goToItem(pathItem)"
                            :key="index"
                            :disabled="index == path.length"
                    >
                        {{ pathItem.basename }}
                    </v-breadcrumbs-item>
                </v-breadcrumbs>

                <v-list class="pb-0">
                    <v-list-tile >
                        <v-list-tile-action>
                            <v-menu offset-y>
                                <v-btn slot="activator" icon><v-icon>more_vert</v-icon></v-btn>
                                <v-list>
                                    <v-list-tile @click="changeVisibleSelected(true)">
                                        <v-list-tile-avatar>
                                            <v-icon>check_circle</v-icon>
                                        </v-list-tile-avatar>
                                        <v-list-tile-action>{{ l18n('select_all') }}</v-list-tile-action>
                                    </v-list-tile>
                                    <v-list-tile @click="changeVisibleSelected(false)">
                                        <v-list-tile-avatar>
                                            <v-icon>check_circle_outline</v-icon>
                                        </v-list-tile-avatar>
                                        <v-list-tile-title>{{ l18n('deselect_all') }}</v-list-tile-title>
                                    </v-list-tile>
                                </v-list>
                            </v-menu>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ l18n('name') }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-divider></v-divider>
                </v-list>

            </v-flex>

            <v-flex fill-height style="position: relative;">
                <div class="scroll-container">
                    <v-layout v-show="libraryState == libraryStates.LOADING" justify-center >
                        <v-progress-circular :size="50" indeterminate color="primary"></v-progress-circular>
                    </v-layout>

                    <v-list class="pt-0">
                        <v-list-tile
                                v-show="!(path === root.basename || !isLibraryAvailable)"
                                @click.stop="goBack"
                        >
                            <v-list-tile-avatar>
                                <v-icon>arrow_back</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-title>{{ l18n('back') }}</v-list-tile-title>
                        </v-list-tile>

                        <v-list-tile v-show="!visibleItems.length && (libraryState != libraryStates.LOADING)">
                            <v-list-tile-avatar></v-list-tile-avatar>
                            <v-list-tile-title>{{ l18n('list_empty') }}</v-list-tile-title>
                        </v-list-tile>

                        <v-list-tile avatar :key="item.basename"
                                     v-for="item in visibleItems"
                                     href="javascript:;"
                        >
                            <v-list-tile-action>
                                <v-checkbox v-model="item.isSelected"></v-checkbox>
                            </v-list-tile-action>
                            <v-list-tile-avatar>
                                <v-icon v-if="item.type === mediaTypes.FOLDER">folder</v-icon>
                                <v-avatar v-else-if="isImage(item)"
                                          :size="48"
                                >
                                    <img :src="fullPath(item)">
                                </v-avatar>
                                <v-icon v-else="item.type === mediaTypes.FOLDER">file_copy</v-icon>
                            </v-list-tile-avatar>
                            <v-list-tile-content
                                    @dblclick="onOpenItem(item)"
                                    @click="onSelectItem(item)"
                            >
                                <v-list-tile-title>{{ item.basename }}</v-list-tile-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-menu offset-y>
                                    <v-btn slot="activator" icon><v-icon>more_vert</v-icon></v-btn>
                                    <v-list>
                                        <v-list-tile @click="deleteItems(item)">
                                            <v-list-tile-avatar>
                                                <v-icon>delete</v-icon>
                                            </v-list-tile-avatar>
                                            <v-list-tile-action>{{ l18n('delete') }}</v-list-tile-action>
                                        </v-list-tile>
                                        <v-list-tile v-show="isImage(item)" @click="cropItem(item)">
                                            <v-list-tile-avatar>
                                                <v-icon>crop</v-icon>
                                            </v-list-tile-avatar>
                                            <v-list-tile-title>{{ l18n('crop') }}</v-list-tile-title>
                                        </v-list-tile>
                                        <v-list-tile @click="renameItem(item)">
                                            <v-list-tile-avatar>
                                                <v-icon>edit</v-icon>
                                            </v-list-tile-avatar>
                                            <v-list-tile-title>{{ l18n('rename') }}</v-list-tile-title>
                                        </v-list-tile>
                                        <v-list-tile v-show="isImage(item)" @click="previewImage(item)">
                                            <v-list-tile-avatar>
                                                <v-icon>share</v-icon>
                                            </v-list-tile-avatar>
                                            <v-list-tile-title>{{ l18n('view') }}</v-list-tile-title>
                                        </v-list-tile>
                                        <v-list-tile v-show="item.type === mediaTypes.ITEM" href="javascript:;">
                                            <v-list-tile-avatar>
                                                <v-icon>cloud_download</v-icon>
                                            </v-list-tile-avatar>
                                            <v-list-tile-title><a :href="fullPath(item)" download>{{ l18n('download') }}</a></v-list-tile-title>
                                        </v-list-tile>
                                    </v-list>
                                </v-menu>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </div>
            </v-flex>
        </v-layout>

        <v-dialog
                v-model="previewDialog.active"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-card>
                <v-toolbar card flat dense color="primary">
                    <v-btn icon @click="previewDialog.active = false">
                        <v-icon>clear</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ previewDialog.item.basename }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-card-text class="text-xs-center">
                    <img :src="fullPath(previewDialog.item)" style="max-width: 100%; display: inline-block;">
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-dialog v-model="renameDialog.active" max-width="500px">
            <v-card>
                <v-card-title>{{ l18n('rename') }}</v-card-title>
                <v-card-text>
                    <v-text-field
                            :label="l18n('name')"
                            v-model.trim="renameDialog.value"
                            autofocus
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            :disabled="(renameDialog.value === '') || renameDialog.loading"
                            :loading="renameDialog.loading" color="success" flat
                            @click="submitRenameDialog">{{ l18n('rename') }}</v-btn>
                    <v-btn
                            :disabled="renameDialog.loading" color="red" flat
                            outlined @click.stop="renameDialog.active = false">{{ l18n('cancel') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="createFolderDialog.active" max-width="500px">
            <v-card>
                <v-card-title>{{ l18n('create_folder') }}</v-card-title>
                <v-card-text>
                    <v-text-field
                            :label="l18n('name')"
                            v-model.trim="createFolderDialog.value"
                            autofocus
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            :disabled="(createFolderDialog.value === '') || createFolderDialog.loading"
                            :loading="createFolderDialog.loading" color="success" flat
                            @click="submitCreateFolderDialog"
                    >{{ l18n('create') }}</v-btn>
                    <v-btn
                            :disabled="createFolderDialog.loading" color="red" flat
                            outlined @click.stop="createFolderDialog.active = false"
                    >{{ l18n('cancel') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="approveDialog.active" max-width="500px">
            <v-card>
                <v-card-title>{{ approveDialog.title }}</v-card-title>
                <v-card-text>
                    <p>{{ approveDialog.text }}</p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="success" flat @click.stop="submitApproveDialog">{{ l18n('approve') }}</v-btn>
                    <v-btn color="red" flat outlined d @click.stop="cancelApproveDialog">{{ l18n('cancel') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog
                v-model="cropDialog.active"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-card>
                <v-toolbar card>
                    <v-toolbar-title>{{ l18n('edit') }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn color="success" @click="submitCropDialog">{{ l18n('save') }}</v-btn>
                    <v-btn color="red" flat v-show="!hasCropperSetting" @click="cancelCropDialog">{{ l18n('cancel') }}</v-btn>
                </v-toolbar>
                <v-card-text v-show="!hasCropperSetting" class="text-xs-center px-0 py-1" style="flex-shrink: 0;">
                    <v-btn flat @click="setCropperAspect( 16 / 6 )">16:9</v-btn>
                    <v-btn flat @click="setCropperAspect( 4 / 3 )">4:3</v-btn>
                    <v-btn flat @click="setCropperAspect( 1 )">1:1</v-btn>
                    <v-btn flat @click="setCropperAspect( 2 / 3 )">2:3</v-btn>
                    <v-btn flat @click="setCropperAspect( 'free' )">{{ l18n('free_size') }}</v-btn>
                </v-card-text>
                <v-container fluid class="px-0 py-0">
                    <img ref="cropper" >
                </v-container>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import MediaTypes from './../utils/types';
    import MediaUtils from './../utils';
    import FormBehaviorTypes from './../../utils/types';
    import CrudApi from './../../api';
    import CrudUrls from './../../api/urls';
    import LibraryBuilder from './../builder';

    let Dropzone = require('dropzone');
    import Cropper from 'cropperjs';

    let LibraryStates = {
        BROWSE: 'browse',
        LOADING: 'loading'
    };

    var URL = window.URL || window.webkitURL;

    export default {
        name: 'media-library',
        props: {
            options: {
                type: Object,
                default: null
            }
        },
        data: function () {
            return {
                dz: null,

                items: [],

                root: { basename: 'uploads', dirname: '', type: 'folder'},

                currentItem: undefined,

                path: '',

                libraryState: LibraryStates.BROWSE,

                uploadProgress: {
                    active: false,
                    value: 0
                },

                title: '',

                search: {
                    value: ''
                },

                hash: Date.now(),

                renameDialog: {
                    active: false,
                    loading: false,
                    item: {},
                    value: ''
                },
                createFolderDialog: {
                    active: false,
                    loading: false,
                    value: ''
                },
                approveDialog: {
                    title: '',
                    text: '',
                    active: false,
                    onApprove: undefined
                },
                cropDialog: {
                    active: false,
                    onApprove: undefined,
                    onCancel: undefined
                },
                previewDialog: {
                    active: false,
                    item: {}
                }
            }
        },
        computed: {
            hasCropperSetting(){
                return this.options.crudField
                    && this.options.crudField.additional
                    && this.options.crudField.additional.cropper;
            },
            mediaTypes(){
                return MediaTypes;
            },
            visibleItems(){

                if (this.search.value){
                    return _.filter(this.items, (i)=>{

                        return (i.type === MediaTypes.FOLDER ) ||
                        (i.type === MediaTypes.ITEM && (_.lowerCase(i.basename).indexOf(this.search.value) >= 0 ))
                    });
                } else {
                    return this.items;
                }
            },
            libraryStates(){
                return LibraryStates;
            },
            isLibraryAvailable(){
                return this.libraryState === LibraryStates.BROWSE;
            },
            formBehaviorTypes(){
                return FormBehaviorTypes;
            },
            isPickMode(){
                return this.options.type !== FormBehaviorTypes.BROWSE;
            },
            isPickFolderMode(){
                return this.options.type === FormBehaviorTypes.PICK_FOLDER;
            },
            pickedItems(){

                return _.filter(this.items, i => i.isSelected );
            },
            pathItems(){

                let joinedPath = '';

                let pathArr = _.map(_.split(this.path, '/'), (item, i, arr)=>{

                    joinedPath += (i > 0 ? '/' : '') + item;

                    return {
                        basename: i === 0 ? this.l18n('media_library_title') : item,
                        dirname: joinedPath
                    }
                });

                return pathArr;
            }



        },
        methods: {
            changeVisibleSelected(isSelected){

                _.each(this.visibleItems, (i)=>{
                    i.isSelected = isSelected;
                });
            },

            uploadTrigger(){
                this.dz.hiddenFileInput.click();
            },

            previewImage(item){
                this.previewDialog.item = item;
                this.previewDialog.active = true;
            },

            pickItems(){

                this.options.pickItems(
                    _.map(
                        _.filter(this.pickedItems, p=>p.type === MediaTypes.ITEM),
                        i=>this.storagePath(i)
                    )
                );

                this.options.closeLibrary();
            },

            closeLibrary(){

                this.options.closeLibrary();
            },

            setCropperAspect(aspect){
                this.cropper.setAspectRatio(aspect);
            },
            cropItem(item){

                this.cropper.replace(this.fullPath(item));

                this.showCropDialog(()=>{

                    CrudApi.mediaItemCrop({
                        item: item,
                        crop_data: this.cropper.getData(true)
                    })
                    .then(()=>{
                        this.getItems();
                        AdminManager.showSuccess(this.l18n('success_completed'));
                    }).catch((error)=>{

                        AdminManager.showError(this.l18n('action_error') + ': ' + error);
                    });
                });
            },
            submitCropDialog(){
                if (this.cropDialog.onApprove) this.cropDialog.onApprove();

                this.cancelCropDialog();
            },
            cancelCropDialog(){
                this.cropDialog.active = false;
            },

            showCropDialog(onApprove, onCancel){
                this.cropDialog.onApprove = onApprove;
                this.cropDialog.onCancel = onCancel;
                this.cropDialog.active = true;
            },

            showApproveDialog(title, text, onApprove){
                this.approveDialog.title = title;
                this.approveDialog.text = text;
                this.approveDialog.onApprove = onApprove;
                this.approveDialog.active = true;
            },

            submitApproveDialog(){
                if (this.approveDialog.onApprove) {
                    this.approveDialog.onApprove();
                }

                this.cancelApproveDialog();
            },

            cancelApproveDialog(){
                this.approveDialog.onApprove = undefined;
                this.approveDialog.active = false;
            },

            currentItemFullPath(){
                return this.fullPath(this.currentItem);
            },
            storagePath(item){
                return item ? App.uploadPath + '/' + item.dirname + '/' + item.basename : '';
            },
            fullPath(item){
                return App.baseUrl + this.storagePath(item);
            },
            isImage(item){

                return MediaUtils.isImageByExt(item.extension);
            },

            isCurrentItemImage(){

                return this.isImage(this.currentItem.title);
            },

            selectAll(){

                _.each(this.items, (item)=>{ this.onSelectItem(item)});
            },
            newFolder(){

                this.createFolderDialog.value = '';
                this.createFolderDialog.active = true;
            },
            renameItem(item){

                this.renameDialog.item = item;
                this.renameDialog.value = [item.basename].join('');
                this.renameDialog.active = true;
            },
            moveItem(){

                if (this.isPickFolderMode){

                    this.options.pickItems( this.path );

                    this.options.closeLibrary();
                } else {

                    let libraryComponent = new LibraryBuilder(FormBehaviorTypes.PICK_FOLDER)
                        .setCrudField(this.field)
                        .build();

                    libraryComponent.options.closeLibrary = ()=> AdminManager.unmountComponent( libraryComponent );
                    libraryComponent.options.pickItems = (path)=>{

                        CrudApi.mediaItemsMove({
                            items: this.pickedItems,
                            path: path
                        })
                        .then(()=>{

                            this.getItems();
                            AdminManager.showSuccess(this.l18n('success_moved'));
                        }).catch((error)=>{

                            AdminManager.showError(this.l18n('action_error') + ': ' + error);
                        });
                    };

                    AdminManager.mountComponent( libraryComponent, false );
                }
            },

            submitCreateFolderDialog(){

                this.createFolderDialog.loading = true;

                CrudApi.mediaFolderNew({
                    root: this.path,
                    title: this.createFolderDialog.value
                }).then(()=>{
                    this.createFolderDialog.loading = false;
                    this.createFolderDialog.active = false;

                    this.getItems();
                })
                .catch((error)=>{
                    this.createFolderDialog.loading = false;
                    this.createFolderDialog.active = false;


                    AdminManager.showError(this.l18n('action_error') + ': ' + error);
                });
            },
            submitRenameDialog(){
                this.renameDialog.loading = true;

                CrudApi.mediaFolderRename({
                    item: this.renameDialog.item,
                    title: this.renameDialog.value
                })
                .then(() => {
                    this.renameDialog.loading = false;
                    this.renameDialog.active = false;
                    this.getItems();
                })
                .catch((error) => {
                    this.renameDialog.loading = false;
                    this.renameDialog.active = false;

                    AdminManager.showError(this.l18n('action_error') + ': ' + error);
                });
            },

            deleteItems(item = null){

                let items = item ? [ item ] : this.pickedItems;

                this.showApproveDialog( this.l18n('delete_action') , item ? item.basename : (this.l18n('picked_files') + ' - ' + items.length), ()=>{
                    CrudApi.mediaItemsDelete({
                        items: items,
                        media_settings: this.options.crudField && this.options.crudField.additional ? this.options.crudField.additional : {}
                    })
                    .then(()=>{
                        this.getItems();
                        AdminManager.showSuccess( this.l18n('success_delete') );
                    }).catch((error)=>{
                        AdminManager.showError(error);
                    });
                });
            },

            onOpenItem(item){
                if (item.type === MediaTypes.FOLDER){
                    this.path = item.dirname + '/' + item.basename;

                    this.getItems();
                }

                if (item.type === MediaTypes.ITEM && ((this.options.type === FormBehaviorTypes.PICK))){
                    this.onSelectItem(item);
                    this.pickItem();
                }
            },
            onSelectItem(item){
                if (!this.isLibraryAvailable)
                    return;

                if (item.type === MediaTypes.ITEM){
                    if (this.options.type === FormBehaviorTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i.basename !== item.basename) i.isSelected = false;
                        });
                    }
                }

                item.isSelected = !item.isSelected;

                this.currentItem = item;
            },

            goBack(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.pathItems.length > 1){
                    this.path = this.pathItems[this.pathItems.length - 2].dirname;
                    this.getItems();
                }
            },

            goToItem(pathItem){

                if (!this.isLibraryAvailable)
                    return;

                this.path = pathItem.dirname;
                this.getItems();
            },

            getItems(source = 'both', save_path = true ){
                this.libraryState = LibraryStates.LOADING;
                this.items = [];

                this.hash = Date.now();

                if (this.isPickFolderMode) {
                    source = 'dir';
                    save_path = false;
                }

                CrudApi.mediaGetItems({ path: this.path, source: source, save_path: save_path})
                    .then((response)=>{
                        this.libraryState = LibraryStates.BROWSE;

                        this.items = _.map(response.data, i=>{
                            this.$set(i, 'isSelected', false);
                            return i;
                        });

                        this.currentItem = undefined;

                        App.mediaPath = this.path;
                    })
                    .catch((error)=>{
                        this.libraryState = LibraryStates.BROWSE;
                        AdminManager.showError(this.l18n('action_error') + ': ' + error);
                    });
            }
        },
        beforeMount(){

            if (this.options.crudField)
                this.title = this.l18n('edit') + ' - ' + this.options.crudField.caption;

            if (App.mediaPath){
                this.path = App.mediaPath;
                this.getItems();
            } else {

                this.path = this.root.dirname;
                this.getItems();
            }
        },
        mounted() {

            this.title = this.l18n('media_library_title');

            this.cropper = new Cropper(this.$refs.cropper, {
                viewMode: 1,
                dragMode: "move"
            });

            if (this.hasCropperSetting)
            {
                if (this.options.crudField.additional.cropper.aspect){

                    if (this.options.crudField.additional.cropper.aspect == '16:9') this.cropper.setAspectRatio( 16 / 9 );
                    if (this.options.crudField.additional.cropper.aspect == '4:3') this.cropper.setAspectRatio( 4 / 3 );
                    if (this.options.crudField.additional.cropper.aspect == '1:1') this.cropper.setAspectRatio( 1 );
                    if (this.options.crudField.additional.cropper.aspect == '2:3') this.cropper.setAspectRatio( 2 / 3 );
                }
            }

             this.dz = new Dropzone(
                document.getElementById('upload'),
                {
                    url: CrudUrls.media.UPLOAD,
                    autoProcessQueue: !(this.options.crudField && this.options.crudField.additional && this.options.crudField.additional.cropper),
                    createImageThumbnails: false,
                    previewsContainer: this.$refs.uploadPreview,
                    totaluploadprogress: (uploadProgress, totalBytes, totalBytesSent)=>{
                        this.uploadProgress.value = uploadProgress;
                        if(uploadProgress === 100)
                            this.uploadProgress.active = false;
                    },
                    processing: ()=>{
                        this.uploadProgress.active = true;
                    },
                    sending: (file, xhr, formData) => {
                        formData.append("_token", CSRF_TOKEN);
                        formData.append("path", this.path);
                        if (this.options.crudField && this.options.crudField.additional &&
                            (this.options.crudField.additional.resize || this.options.crudField.additional.thumbnails))
                            formData.append("media_settings", JSON.stringify(this.options.crudField.additional));

                        if (file.cropData) formData.append("crop_data", JSON.stringify(file.cropData) );
                    },
                    addedfiles: (files)=>{

                        if (this.hasCropperSetting) {

                            if (files.length == 1 && ( files[0].type.indexOf('image') != -1)){

                                let objectUrl = URL.createObjectURL(files[0]);

                                this.cropper.replace(objectUrl);

                                this.showCrop(()=>{

                                    files[0].cropData = this.cropper.getData(true);

                                    this.dz.processQueue();
                                });
                            } else {
                                this.dz.processQueue();
                            }
                        }
                    },
                    success: function(e, res){
                        if(res.success){
                            AdminManager.showSuccess(this.l18n('uploaded') + ': ' + res.message);
                        } else {
                            AdminManager.showError(this.l18n('action_error') + ': ' + res.message);
                        }
                    },
                    error: function(e, res, xhr){
                        AdminManager.showError(this.l18n('action_error') + ': ' + e);
                    },
                    queuecomplete: ()=>{
                        this.getItems();
                    }
                }
            );

        }
    }
</script>

<style>
    .cropper-bg{
        background-repeat: repeat;
    }
</style>
