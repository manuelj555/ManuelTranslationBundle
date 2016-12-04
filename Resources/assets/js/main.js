import Vue from "vue";
import TransList from "../vue/components/TransList.vue";
import VueI18n from "vue-i18n";
import locales from "./locales.js";
// import TranslationDataPlugin from '../vue/plugins/TranslationData.js'

Vue.use(VueI18n)
Vue.config.lang = TranslationData.locale
Vue.locale(TranslationData.locale, locales[TranslationData.locale])

Vue.filter('boolean', function (value) {
    return value ? Vue.t('label.yes') : Vue.t('label.no')
})

// Vue.use(TranslationDataPlugin, TranslationData)

let app = new Vue({
    el: "#translations-container",
    data: {
        name: 'Manuel',
    },
    methods: {
        add() {
            this.$refs.list.add()
        },
    },
    components: {TransList}
})
