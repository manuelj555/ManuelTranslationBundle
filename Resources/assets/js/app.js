import Vue from 'vue'
import TransList from '../vue/components/TransList.vue'
import VueI18n from 'vue-i18n'
import locales from './locales.js'

Vue.use(VueI18n)
Vue.config.lang = TranslationData.locale
Vue.locale(TranslationData.locale, locales[TranslationData.locale])

Vue.filter('boolean', function(value) {
	return value ? Vue.t('label.yes') : Vue.t('label.no')
})

let app = new Vue({
	el: '#translations-container',

	data () {
		return Object.assign({
			message: {
				content: '',
				type: null
			}
		}, TranslationData)
	},

	methods: {
		addTranslation () {
			//this.$dispatch('show-success-message', 'Hola Manuel')
		},
	},

	components: {TransList},
})