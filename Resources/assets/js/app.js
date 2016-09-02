import Vue from 'vue'
import TransList from '../vue/components/TransList.vue'

Vue.filter('boolean', function(value) {
	return value ? 'Yes' : 'No'
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

	computed: {
	},

	methods: {
		addTranslation () {
			//this.$dispatch('show-success-message', 'Hola Manuel')
		},
	},

	events: {
		/*'translation-saved' (row) {
			this.message = {
				message: 'Translation "%code%" saved!'.replace('%code%', row.code),
				type: 'success',
				time: 2000
			};
		}*/
	},

	components: {TransList},
})