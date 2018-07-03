
Vue.component('test-component', require('./test-component.vue'));
Vue.component('editpanel-user-component', require('./editpanel-user-component.vue'));

AdminManager.registerMiddleware( (event, component, next)=>{

    if (event == 'crud:on:mount') {

        component.addComponents(
            [
                {
                    id: 'test-component',
                    name: 'test-component',
                    options: {
                        message: 'i am user component'
                    }
                }
            ]
        );
    }

    next();
});

AdminManager.registerMiddleware( (event, component, next)=>{

    if (event == 'editpanel:on:mount') {

        component.addComponents(
            [
                {
                    id: 'editpanel-user-component',
                    name: 'editpanel-user-component',
                    options: {
                        message: 'i am user component'
                    }
                }
            ]
        );
    }

    next();
});

let userComponent = {
    id: 'user-component-1',
    name: 'test-component',
    options: {
        isModal: false,
        counterStartValue: 100
    }
};

Bus.$on('user:testcomponent:mount', ()=> AdminManager.mountComponent( userComponent , true) );