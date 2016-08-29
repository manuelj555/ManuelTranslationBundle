<template>

<trans-item v-for="item in items" 
:item="item" :locales="locales"></trans-item>

</template>

<script>
import Vue from 'vue'
import TransItem from './TransItem.vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

export default {
    props: {
        locales: {required: true, type: [Array, Object]},
        baseApiUrl: {required: true, type: [String]}
    },

	data () {
		return {
            items: {},
        }
	},

    ready () {
        this.resource = this.$resource(this.baseApiUrl + '{id}')

        this.getTranslations()
    },

    methods: {
        getTranslations () {
            return this.resource.get().then((res) => {
                this.$set('items', res.json());
            })
        },

        save (item) {
            this.resource.save({id: item.id}, item).then(response => {
                this.$dispatch('translation-saved', response.json())
                this.getTranslations().then(() => {
                    this.$broadcast('translation-saved.complete', response.json())                    
                })
            })
        },
    },

    events: {
        'save-item' (item) {
            this.save(item)
        } 
    },

    components: {TransItem}
}
</script>