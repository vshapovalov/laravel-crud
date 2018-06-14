<template>
    <div v-if="active === true" class="media-library" :class="{modal: isPickMode, 'is-active': isPickMode}" style="z-index: 100000;">
        <div :class="{'modal-background': isPickMode}"></div>
        <div :class="{'modal-content' : isPickMode}" >

            <div :class="{'container': isPickMode, 'is-fluid': isPickMode}">
                <div class="panel">
                        <div class="panel-heading">
                        <p class="title is-5">{{ title }}</p>
                    </div>
                    <div class="panel-block">
                        <div class="">
                            <a v-show="!isPickFolderMode" ref="upload" class="button is-success" :class="{'is-static': !isLibraryAvailable}">
                                <div id="uploadPreview" style="display: none">

                                </div>Загрузить</a>
                            <a :class="{'is-static': path === root.basename || !isLibraryAvailable}"
                               class="button is-warning" @click="goBack">Назад</a>
                            <a :class="{'is-static': !isLibraryAvailable}"
                               class="button is-warning" @click="newFolder">Создать папку</a>
                            <a :class="{'is-static': !currentItem || !isLibraryAvailable}"
                               class="button is-warning" @click="renameItem">Переименовать</a>
                            <a :class="{'is-static': (!currentItem && !isPickFolderMode)|| !isLibraryAvailable}"
                               class="button is-warning" @click="moveItem">{{ isPickFolderMode ? 'Переместить сюда' : 'Переместить' }}</a>
                            <a :class="{'is-static': (!currentItem && !isPickFolderMode)|| !isLibraryAvailable}"
                               class="button is-warning" @click="cropItem">Обрезать</a>
                            <a :class="{'is-static': !currentItem || !isLibraryAvailable}"
                               class="button is-warning" @click="deleteItem">Удалить</a>
                            <a v-if="isPickMode && !isPickFolderMode" :class="{'is-static': pickedItems.length === 0} || !isLibraryAvailable"
                               class="button is-warning" @click="pickItem">Выбрать</a>
                            <a v-if="isPickMode" class="button is-danger" @click="closeLibrary">Закрыть</a>
                            <div class="field is-inline-block">
                                <p class="control has-icons-right">
                                    <input class="input" type="text" placeholder="" v-model.trim="search.value">
                                    <span class="icon is-small is-right">
                                        <i class="fa fa-search"></i>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div ref="progressgroup" class="panel-block" style="display: none;">
                        <progress ref="progress" class="progress is-primary" :value="progressValue" max="100">{{progressValue}}%</progress>
                    </div>
                    <div class="panel-block">
                        <div class="breadcrumb" >
                            <ul>
                                <li v-for="(pathItem, index) in pathItems" @click="goToItem(pathItem)" :class="{'is-active': index == (path.length - 1)}">
                                    <a>{{ pathItem.basename }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-block is-block">
                        <div class="columns">
                            <div class="column is-three-quarters">
                                <table class="table is-fullwidth" >
                                    <tr>
                                        <th data-role="button" @click="selectAll">*</th>
                                        <th>Название</th>
                                    </tr>

                                    <tr v-if="pathItems.length > 1 && isLibraryAvailable" @dblclick.stop="goBack">
                                        <td>
                                            <span><i class="fa fa-arrow-up"></i></span>
                                        </td>
                                        <td><span class="is-unselectable">...</span></td>
                                    </tr>
                                    <tr v-for="item in visibleItems" @dblclick.stop.prevent="onOpenItem(item)" @click.stop.prevent="onSelectItem(item)"
                                        :class="[{'is-selected': currentItem && item.basename === currentItem.basename},{ 'notification is-success': item.isSelected}]">
                                        <td>
                                            <span v-if="item.type === mediaTypes.FOLDER">
                                                <i class="fa fa-folder"></i>
                                            </span>
                                            <span v-else-if="isImage(item)" class="image is-16x16">
                                                <img :src="fullPath(item)" alt="">
                                            </span>
                                            <span v-else >
                                                <i class="fa fa-file"></i>
                                            </span>
                                        </td>
                                        <td><span class="is-unselectable">{{ item.basename }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="column">
                                <div v-if="currentItem">
                                    <p class="title is-6">{{ currentItem.basename }}</p>

                                    <div v-if="currentItem.type === mediaTypes.ITEM">
                                        <div>
                                            <a :href="fullPath(currentItem)+'?hash=' + hash">Ссылка</a>
                                        </div>
                                        <figure class="image" v-if="isCurrentItemImage">
                                            <img :src="fullPath(currentItem)+'?hash=' + hash" alt="">
                                        </figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div v-if="editForm.state != editStates.NONE" class="modal is-active">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head">
                            <p class="modal-card-title">{{ editForm.title }}</p>
                        </header>
                        <section class="modal-card-body">
                            <div class="edit-form">
                                <div class="field ">
                                    <label class="label">Название</label>
                                    <div class="control">
                                        <input type="text" class="input" v-model="editForm.value">
                                    </div>
                                </div>
                            </div>
                        </section>
                        <footer class="modal-card-foot">
                            <a :class="{'is-static': !editForm.value, 'is-loading': editForm.state === editStates.POSTING }" class="button is-success" @click="submitEditForm">Подтвердить</a>
                            <a class="button is-danger is-outlined" @click="cancelEdit">Отмена</a>
                        </footer>
                    </div>
                </div>

                <div v-if="modalApprove.state != editStates.NONE" class="modal is-active">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head">
                            <p class="modal-card-title">{{ modalApprove.title }}</p>
                        </header>
                        <section class="modal-card-body">
                            <p class="modal-card-title">{{ modalApprove.text }}</p>
                        </section>
                        <footer class="modal-card-foot">
                            <a class="button is-success" @click="modalApproveOk">Подтвердить</a>
                            <a class="button is-danger is-outlined" @click="modalApproveCancel">Отмена</a>
                        </footer>
                    </div>
                </div>

                <div v-show="cropForm.state != editStates.NONE" class="modal is-active">
                    <div class="modal-background"></div>
                    <div class="modal-card" style="width: 80%;">
                        <header class="modal-card-head">
                            <p class="modal-card-title">Редактировать изображение</p>
                        </header>
                        <section class="modal-card-body flex" style="padding: 0;">
                            <div class="flex-grow">
                                <img ref="cropper" >
                            </div>
                            <div v-show="!hasCropperSetting" class="flex-noshrink w-200px pad-10">
                                <a href="javascript:;" class="button is-primary w100 mb-10" @click.prevent="setCropperAspect( 16 / 6 )">16:9</a>
                                <a href="javascript:;" class="button is-primary w100 mb-10" @click.prevent="setCropperAspect( 4 / 3 )">4:3</a>
                                <a href="javascript:;" class="button is-primary w100 mb-10" @click.prevent="setCropperAspect( 1 )">1:1</a>
                                <a href="javascript:;" class="button is-primary w100 mb-10" @click.prevent="setCropperAspect( 2 / 3 )">2:3</a>
                                <a href="javascript:;" class="button is-primary w100" @click.prevent="setCropperAspect( 'free' )">Free</a>
                            </div>
                        </section>
                        <footer class="modal-card-foot">
                            <a class="button is-success" @click="cropperSave">Сохранить</a>
                            <a v-show="!hasCropperSetting" class="button is-danger is-outlined" @click="cropperCancel">Отмена</a>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    // TODO: refactor simple modal to component

    import MediaTypes from './../utils/types';
    import MediaUtils from './../utils';
    import FormBehaviorTypes from './../../utils/types';
    import CrudApi from './../../api';
    import CrudUrls from './../../api/urls';
    import LibraryBuilder from './../builder';
    let Dropzone = require('dropzone');
    import Cropper from 'cropperjs';

    let EditStates = {
        NONE: '',
        NEW_FOLDER: 'newfolder',
        RENAME_FOLDER: 'renamefolder',
        MOVE_ITEM: 'moveitem',
        CROP_IMAGE: 'cropper',
        POSTING: 'posting'
    };

    let LibraryStates = {
        BROWSE: 'browse',
        LOADING: 'loading'
    };

    var URL = window.URL || window.webkitURL;

    export default {
        name: 'media-library',
        props: {
            active: {
                default: true
            },
            type:{
                type: String
            },
            crudField:{
                type: Object,
                default: null
            }
        },
        data: function () {
            return {
                dz: null,
                items: [],
                root: { basename: 'uploads', dirname: '', type: 'folder'},
                currentFolder: undefined,
                currentItem: undefined,
                path: '',
                editForm: {
                    state: EditStates.NONE,
                    title: '',
                    value: ''
                },
                libraryState: LibraryStates.BROWSE,
                dropZone: {},
                progressValue: 0,
                modalApprove: {
                    title: '',
                    text: '',
                    state: EditStates.NONE,
                    onApprove: undefined
                },

                cropForm: {
                    state: EditStates.NONE,
                    onApprove: undefined,
                    onCancel: undefined
                },

                modalSelectDir:{
                    title: '',
                    text: '',
                    items: [],
                    state: EditStates.NONE,
                    onApprove: undefined
                },
                baseUrl: '',
                title: 'Медиа библиотека',
                search: {
                    value: ''
                },
                hash: Date.now()
            }
        },
        computed: {
            hasCropperSetting(){
                return this.crudField && this.crudField.additional && this.crudField.additional.cropper;
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
            editStates(){
                return EditStates;
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
                return this.type !== FormBehaviorTypes.BROWSE;
            },
            isPickFolderMode(){
                return this.type === FormBehaviorTypes.PICK_FOLDER;
            },
            pickedItems(){

                return _.filter(this.items, (i) => {
                    return (i.type === MediaTypes.ITEM) && i.isSelected
                });

            },
            pathItems(){

                let joinedPath = '';

                let pathArr = _.map(_.split(this.path, '/'), (item, i, arr)=>{

                    joinedPath += (i > 0 ? '/' : '') + item;

                    return {
                        basename: i === 0 ? 'Библиотека' : item,
                        dirname: joinedPath
                    }
                });

                return pathArr;
            }



        },
        methods: {

            /******************************** behavior *********************************/

            pickItem(){
                let selectedItems = _.map(this.pickedItems, (i)=>this.storagePath(i));
                this.$emit("pick", selectedItems);
            },

            closeLibrary(){
                this.$emit("cancel");
            },

            /******************************** end behavior *********************************/

            /******************************** modal **********************************/

            setCropperAspect(aspect){
                this.cropper.setAspectRatio(aspect);
            },
            cropItem(){

                if (this.currentItem){

                    this.cropper.replace(this.fullPath(this.currentItem));

                    this.showCrop(()=>{

                        CrudApi.mediaItemCrop({
                            item: this.currentItem,
                            crop_data: this.cropper.getData(true)
                        })
                        .then(()=>{
                                this.getItems();
                                toastr.success('Успешно выполнено');
                        }).catch((error)=>{

                            toastr.error(error);
                        });
                    });
                }
            },
            cropperSave(){
                if (this.cropForm.onApprove) this.cropForm.onApprove();

                this.cropperCancel();
            },
            cropperCancel(){
                this.cropForm.state = EditStates.NONE;
            },

            showCrop(onApprove, onCancel){
                this.cropForm.onApprove = onApprove;
                this.cropForm.onCancel = onCancel;
                this.cropForm.state = EditStates.POSTING;
            },

            showModal(title, text, onApprove){
                this.modalApprove.title = title;
                this.modalApprove.text = text;
                this.modalApprove.onApprove = onApprove;
                this.modalApprove.state = EditStates.POSTING;
            },

            modalApproveOk(){
                if (this.modalApprove.onApprove) {
                    this.modalApprove.onApprove();
                }

                this.modalApproveCancel();
            },

            modalApproveCancel(){
                this.modalApprove.onApprove = undefined;
                this.modalApprove.state = EditStates.NONE;
            },

            /******************************** end modal **********************************/



            currentItemFullPath(){
                return this.fullPath(this.currentItem);
            },
            storagePath(item){
                return uploadPath + '/' + item.dirname + '/' + item.basename;
            },
            fullPath(item){
                return baseUrl + this.storagePath(item);
            },
            isImage(item){
                return MediaUtils.isImageByExt(item.extension);
            },

            isCurrentItemImage(){
                return this.isImage(this.currentItem.title);
            },

            /******************************** edit actions **********************************/

            selectAll(){
                _.forEach(this.items, (item)=>{ this.onSelectItem(item)});
            },
            newFolder(){
                if (!this.isLibraryAvailable)
                    return;

                this.editForm.title = "Создать папку";
                this.editForm.state = EditStates.NEW_FOLDER;
            },
            renameItem(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.currentItem){
                    this.editForm.title = "Переименовать";
                    this.editForm.value = this.currentItem.basename;
                    this.editForm.state = EditStates.RENAME_FOLDER;
                }
            },
            moveItem(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.isPickFolderMode){

                    this.$emit("pick", this.path);

                } else {
                    new LibraryBuilder(FormBehaviorTypes.PICK_FOLDER)
                        .setCrudField(this.field)
                        .onPick((path)=>{

                            CrudApi.mediaItemsMove({
                                items: this.pickedItems,
                                path: path
                            })
                                .then(()=>{
                                    this.getItems();
                                    toastr.success('Успешно перемещено');
                                }).catch((error)=>{

                                toastr.error(error);
                            });
                        })
                        .build()
                        .show();
                }
            },

            submitEditForm(){
                if ( _.trim(this.editForm.value)){
                    if (this.editForm.state === EditStates.NEW_FOLDER){

                        CrudApi.mediaFolderNew({
                            root: this.path,
                            title: this.editForm.value
                        }).then(()=>{
                            this.cancelEdit();
                            this.getItems();

                        })
                        .catch((error)=>{
                            this.cancelEdit();
                            console.log(error);

                            alert('При выполнении операции возникла ошибка.');
                        });
                    }

                    if (this.editForm.state === EditStates.RENAME_FOLDER){

                        CrudApi.mediaFolderRename({
                                item: this.currentItem,
                                title: this.editForm.value
                            })
                            .then(()=>{
                                this.cancelEdit();
                                this.getItems();
                            })
                            .catch((error)=>{
                                console.log(error);
                                this.cancelEdit();
                                alert('При выполнении операции возникла ошибка.');
                            });
                    }
                }
            },

            deleteItem(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.currentItem){

                    this.showModal('Удаление', 'Подтвердите удаление ' + this.currentItem.basename, ()=>{
                        CrudApi.mediaItemsDelete({
                            item: this.currentItem,
                            media_settings: this.crudField && this.crudField.additional ? this.crudField.additional : {}
                        })
                        .then(()=>{
                            this.getItems();
                            toastr.success('Успешно удалено');
                        }).catch((error)=>{
                            toastr.error(error);
                        });
                    });
                }
            },
            cancelEdit(){
                this.editForm.title = "";
                this.editForm.value = "";
                this.editForm.state = EditStates.NONE;
            },
            /******************************** end edit form **********************************/

            /******************************** common library actions *********************************/
            onOpenItem(item){
                if (item.type === MediaTypes.FOLDER){
                    this.path = item.dirname + '/' + item.basename;

                    this.getItems();
                }

                if (item.type === MediaTypes.ITEM && ((this.type === FormBehaviorTypes.PICK))){
                    this.onSelectItem(item);
                    this.pickItem();
                }
            },
            onSelectItem(item){
                if (!this.isLibraryAvailable)
                    return;

                if (item.type === MediaTypes.ITEM){
                    if (this.type === FormBehaviorTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i.basename !== item.basename)
                                this.$set(i, 'isSelected', false);
                        });
                    }
                }

                this.$set(item, 'isSelected', !item.isSelected);

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
            /******************************** end common  library actions *********************************/


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
                        this.items = response.data;
                        this.currentItem = undefined;
                        mediaPath = this.path;
                    })
                    .catch((error)=>{
                        this.libraryState = LibraryStates.BROWSE;
                        console.log(error);
                    });
            }
        },
        beforeMount(){

            if (this.crudField) this.title = 'Редактирование - ' + this.crudField.caption;

            if (mediaPath){
                this.path = mediaPath;
                this.getItems();
            } else {

                this.path = this.root.dirname;
                this.getItems();
            }
        },
        mounted() {


            this.cropper = new Cropper(this.$refs.cropper, {
                viewMode: 1,
                dragMode: "move",
                preview: this.$refs.cropperpreview
            });


            if (this.hasCropperSetting)
            {
                if (this.crudField.additional.cropper.aspect){

                    if (this.crudField.additional.cropper.aspect == '16:9') this.cropper.setAspectRatio( 16 / 9 );
                    if (this.crudField.additional.cropper.aspect == '4:3') this.cropper.setAspectRatio( 4 / 3 );
                    if (this.crudField.additional.cropper.aspect == '1:1') this.cropper.setAspectRatio( 1 );
                    if (this.crudField.additional.cropper.aspect == '2:3') this.cropper.setAspectRatio( 2 / 3 );
                }
            }

            this.dz = new Dropzone(
                this.$refs.upload,
                {
                    url: CrudUrls.media.UPLOAD,
                    previewsContainer: "#uploadPreview",
                    autoProcessQueue: !(this.crudField && this.crudField.additional && this.crudField.additional.cropper),
                    createImageThumbnails: false,

                    totaluploadprogress: (uploadProgress, totalBytes, totalBytesSent)=>{
                        this.progressValue = uploadProgress;
                        if(uploadProgress === 100){
                            $(this.$refs.progressgroup).delay(1500).slideUp();
                        }
                    },
                    processing: ()=>{
                        this.progressValue = 0;
                        $(this.$refs.progressgroup).fadeIn();
                    },
                    sending: (file, xhr, formData) => {
                        formData.append("_token", CSRF_TOKEN);
                        formData.append("path", this.path);
                        if (this.crudField && this.crudField.additional &&
                            (this.crudField.additional.resize || this.crudField.additional.thumbnails))
                            formData.append("media_settings", JSON.stringify(this.crudField.additional));

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
                            toastr.success(res.message, "Загружено");
                        } else {
                            toastr.error(res.message, "Ошибочка");
                        }
                    },
                    error: function(e, res, xhr){
                        console.log(e);
                        toastr.error(res, "Ошибочка");
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
    .modal-content {
        width: 100%;
        height: 100%;
        background-color: #fff;
        margin: 20px;
        padding: 20px;
        border-radius: 5px;
    }

    .flex{
        display: flex;
    }

    .flex-grow{
        flex-grow: 1;
    }

    .flex-noshrink{
        flex-shrink: 0;
    }

    .w-200px{
        width: 200px;
    }

    .img-responsive{
        max-width: 100%;
        height: auto;
        display: block;
    }

    .w100{
        width: 100%;
    }

    .mb-10{
        margin-bottom: 10px;
    }

    .pad-10{
        padding: 10px;
    }
</style>
