import VuexPersistence from 'vuex-persist';

const vuexPersist = new VuexPersistence({
    storage: window.localStorage,
    modules: ['session'],
});

export default vuexPersist;