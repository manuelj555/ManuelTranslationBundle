import Vue from 'vue'
import TransList from '../vue/components/TransList.vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

let app = new Vue({
	el: '#translations-container',

	data () {
		TranslationData.items = []

		return TranslationData
	},

	ready () {
		this.resource = this.$resource(this.baseUrlApi + '{id}')

		this.getTranslations()
	},

	methods: {
		getTranslations () {
			this.resource.get({}).then((res) => {
				this.$set('items', res.json());
				//this.$set('totalPosts', Number(res.headers['X-Total-Count']));
			})
		}
	},

	components: {TransList}
})