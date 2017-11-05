<template>
    <div class="">
        <div class="columns">
            <div class="column is-2">
                <nav class="panel">
                    <p class="panel-heading">
                        Админ.панель
                    </p>
                    <div class="panel-block">
                        <a @click.stop.prevent="openMediaLibrary">Медиа библиотека</a>
                    </div>
                    <div class="panel-block" v-for="crud in visibleCruds"><a @click.stop.prevent="openCrud(crud)">{{ crud.name }}</a></div>
                </nav>
            </div>
            <div id="workspace" class="column">

            </div>
        </div>
    </div>
</template>

<script>

    import MediaBuilder from './../media/builder';
    import CrudBuilder from './../crud/builder';
    import FormBehaviorTypes from './../utils/types';

    // TODO: check for auth

    export default {
        name: 'admin-manager',
        props: ['cruds'],
        data: function () {
            return {
                activeInstance: undefined
            }
        },
        computed: {
            visibleCruds(){
                return _.filter(this.cruds, (c) => c.visible);
            },
            crudGroups(){
                return _.uniq(_.filter(_.map(this.visibleCruds, (c)=> c.group ? c.group : ''), (g)=> g!==''));
            },
            crudsWithoutGroup(){
                return _.filter(this.visibleCruds, (c)=>!c.group);
            },

        },
        methods:{
            getCrudsByGroup(groupName){
                return _.filter(this.visibleCruds, (c)=>c.group === groupName);
            },



            closeActiveInstance(){
                if (this.activeInstance) {
                    this.activeInstance.close();
                }
            },

            openMediaLibrary(){
                this.closeActiveInstance();
                this.activeInstance = new MediaBuilder(FormBehaviorTypes.BROWSE).build();
                this.activeInstance.show();

            },
            openCrud(crud){
                this.closeActiveInstance();
                this.activeInstance = new CrudBuilder(crud, FormBehaviorTypes.BROWSE).build();
                this.activeInstance.show();
            }
        },
        beforeMount(){

        },
        mounted(){

        }
    }
</script>
