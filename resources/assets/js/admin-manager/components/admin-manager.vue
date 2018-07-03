<template>
    <v-app :light=" appTheme == 'light' " :dark=" appTheme == 'dark' ">
        <transition-group name="v-snack-transition" class="messages layout row wrap">
            <div :key="message.id" v-for="message in messages" class="v-snack v-snack--active v-snack--bottom flex xs12 py-1">
                <div class="v-snack__wrapper" :class="[ message.type ]">
                    <div class="v-snack__content">
                        {{ message.text }}
                        <v-btn flat @click="closeMessage(message)">{{ l18n('close') }}</v-btn>
                    </div>
                </div>
            </div>
        </transition-group>
        <v-navigation-drawer v-model="drawer" app>
            <v-list >
                <v-list-tile avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ user.name }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
            <v-divider></v-divider>

            <v-list dense>
                <v-list-tile v-show="selectedMenuItem" @click="backOnMenu">
                    <v-list-tile-avatar>
                        <v-icon>arrow_back</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ l18n('back') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile
                        v-for="item in activeMenuGroup" :key="item.id"
                        @click="gotoMenu(item)"
                        :class="{'v-list__tile--highlighted': activeMenuItem && (activeMenuItem.id == item.id)}"
                >
                    <v-list-tile-avatar>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ item.caption }}</v-list-tile-title>
                    </v-list-tile-content>
                    <v-spacer></v-spacer>
                    <v-list-tile-action v-show="item.items.length">
                        <v-icon >navigate_next</v-icon>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>

        </v-navigation-drawer>
        <v-toolbar app color="primary">

            <v-tooltip right>
                <v-btn slot="activator" icon @click.stop="drawer = !drawer">
                    <v-icon>menu</v-icon>
                </v-btn>
                <span>{{ l18n('show_hide_menu') }}</span>
            </v-tooltip>

            <v-toolbar-title class="mr-5 align-center">
                <span class="title">{{ l18n('dashboard_title') }}</span>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-tooltip left>
                <v-btn slot="activator" icon @click="logout">
                    <form name="logout" :action="logoutUrl" method="POST" style="display: none">
                        <input type="hidden" name="_token" :value="csrfToken">
                    </form>
                    <v-icon>exit_to_app</v-icon>
                </v-btn>
                <span>{{ l18n('logout') }}</span>
            </v-tooltip>

        </v-toolbar>
        <v-content>
            <v-container fluid fill-height>
                <v-layout id="workspace">
                    <v-flex xs12 :key="component.id" v-for="component in mountedComponents" v-show="!component.options.isModal">
                        <v-dialog
                                v-if="component.options && component.options.isModal"
                                v-model="component.active"
                                fullscreen
                                hide-overlay
                                transition="dialog-bottom-transition"
                                scrollable
                        >
                            <v-card flat class="px-0 py-0">
                                <div :is="component.name" :options="component.options"></div>
                            </v-card>
                        </v-dialog>
                        <div v-else :is="component.name" :options="component.options" ></div>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    import MediaLibraryBuilder from './../media/builder';
    import CrudUtils from './../crud/utils';
    import CrudBuilder from './../crud/builder';
    import CrudEditPanel from './../crud/editpanel';
    import FormBehaviorTypes from './../utils/types';
    import Events from './../../events';

    export default {
        name: 'admin-manager',
        data: function () {
            return {
                activeComponent: undefined,
                cruds: {},
                menu: [],
                additionalComponents: [],
                additionalComponentsLoaded: 0,
                selectedMenuItem: null,
                activeMenuItem: null,
                drawer: true,
                mountedComponents: [],
                middlewares: [],
                messages: []
            }
        },
        computed:{
          appTheme(){ return App.theme.name; },
          logoutUrl(){ return App.baseUrl + '/logout'; },
          csrfToken(){ return CSRF_TOKEN; },
          sortedMenu(){ return _.sortBy(this.menu, ['order', 'caption']); },

          activeMenuGroup(){
              return _.filter(
                  this.sortedMenu,
                  m=> this.selectedMenuItem ? m.parent_id == this.selectedMenuItem.id : !m.parent_id
              );
          },
          user(){
              return App.user;
          }
        },
        methods:{
            logout(){
                document.forms['logout'].submit();
            },
            addMessage(message){

                message.id = CrudUtils.randomInteger(1, 999999);

                if (message.timeout) setTimeout(()=>this.closeMessage( message ), message.timeout);

                this.messages.splice( 0, 0, message);
            },
            closeMessage(message){

                let messageIndex = _.findIndex( this.messages, m=>m.id === message.id);

                if (messageIndex >= 0) this.messages.splice(messageIndex, 1);
            },
            mountComponent(component, unmountActiveComponent){

                component.active = false;

                if ( unmountActiveComponent ){
                    if (this.activeComponent) this.unmountComponent( this.activeComponent );
                    this.activeComponent = component;
                }

                this.mountedComponents.splice(0, 0, component);

                Vue.nextTick(()=>{
                    component.active = true;
                });
            },


            unmountComponentByIndex(component){

                let componentIndex = _.findIndex(this.mountedComponents, c => c.id == component.id);

                this.mountedComponents.splice( componentIndex, 1 );
            },

            unmountComponent(component){

                component.active = false;

                if (component.options.isModal) {
                    setTimeout(() => {
                        this.unmountComponentByIndex( component );
                    }, 1000);
                } else {
                    this.unmountComponentByIndex( component );
                }
            },

            backOnMenu(){
                this.selectedMenuItem = this.selectedMenuItem.parent_id
                    ? _.find( this.menu, m => m.id === this.selectedMenuItem.parent_id)
                    : null;
            },

            gotoMenu(menuItem){
                if ( menuItem.items.length )
                    this.selectedMenuItem = menuItem;

                this.activeMenuItem = menuItem;

                this.$set(menuItem, 'active', true);

                if (menuItem.action)
                    Bus.$emit(menuItem.action, menuItem.action, menuItem.caption);
            },

            mountEditPanel(crud, item, onSave = null, onCancel = null){

                let component = new CrudEditPanel(crud, item, onSave, onCancel).build();

                this.mountComponent(component, false);
            },

            mountMediaLibrary(){

                let libraryComponent = new MediaLibraryBuilder(FormBehaviorTypes.BROWSE).build();

                this.mountComponent( libraryComponent, true );
            },

            mountCrud(action){
                let crudCode = action.split(':')[1];

                let crud = AdminManager.getCrud(crudCode);

                let component = new CrudBuilder(crud, FormBehaviorTypes.BROWSE).build();

                this.mountComponent( component, true );
            },

            onUrlRender(url){

            },
            onUrlGo(url){
                window.location = url;
            },

            onConfigLoaded(config){
                this.cruds = config.list;

                this.menu.splice(0,0,...config.menu);
                this.additionalComponents.splice(0,0,...config.components);
                this.registerDefaultComponents();
                if (this.additionalComponents.length > 0){
                    this.registerUserComponents()
                        .then(()=>{ this.mountDefaultComponent() });
                } else {
                    this.mountDefaultComponent();
                }

            },

            mountDefaultComponent(){

                let defaultMenu = _.find( this.menu, m=>m.default );

                if (defaultMenu)
                    this.gotoMenu(defaultMenu);
            },

            registerDefaultComponents(){

                Bus.$on( Events.MEDIA_LIBRARY.MOUNT, this.mountMediaLibrary);

                Bus.$on( Events.EDIT_PANEL.MOUNT, this.mountEditPanel);

                _.each(this.cruds, (crud)=>{
                    Bus.$on(`crud:${crud.code}:mount`, this.mountCrud);
                });
            },
            registerUserComponents(){
                return new Promise((resolve, reject)=>{
                    let body = document.querySelector('body');

                    _.each(this.additionalComponents, (cmp)=>{
                        let scriptEl = document.createElement('script');
                        body.appendChild(scriptEl);
                        scriptEl.setAttribute('type', 'text/javascript');
                        scriptEl.setAttribute('async', true);
                        scriptEl.onload = ()=>{
                            if (++this.additionalComponentsLoaded == this.additionalComponents.length) resolve();
                        };

                        scriptEl.setAttribute('src', cmp.path);
                    });
                });

            },
            registerMiddleware(middleware){
                this.middlewares.splice(this.middlewares.length, 0, middleware);
            }
        },
        beforeMount(){
            this.$emit('mount', this);

            Bus.$on( Events.ADMIN.INSTANCE_MOUNT, this.mountComponent );
            Bus.$on( Events.ADMIN.INSTANCE_UNMOUNT, this.unmountComponent );
            Bus.$on( Events.ADMIN.MIDDLEWARE_REGISTER, this.registerMiddleware );
            Bus.$on( Events.ADMIN.URL_RENDER, this.onUrlRender );
            Bus.$on( Events.ADMIN.URL_GO, this.onUrlGo );
            Bus.$on( Events.ADMIN.SHOW_MESSAGE, this.addMessage);
        },
        mounted(){
            Bus.$on( Events.ADMIN.CONFIG_LOADED, this.onConfigLoaded );
        }
    }
</script>

<style lang="scss">
    .messages{
        position: fixed;
        z-index: 10000;
        bottom: 0;
        left: 0;
        right: 0;

        .v-snack{
            position: relative;
        }
    }
</style>
