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

        save (item, success, error) {
            this.resource.save({id: item.id}, item).then(response => {
                let data = response.json();
                success && success(data);
                //this.$dispatch('translation-saved', response.json())
                // Por ahora no recargar listado.
                // Creo que no hace falta :)
                /*this.getTranslations().then(() => {
                    //this.$broadcast('translation-saved.complete', response.json())                    
                })*/
            }, response => {
                error && error(response)
            })
        },
    },

    events: {
        'save-item' (item, success, error) {
            this.save(item, success, error)
        } 
    },

    components: {TransItem}
}
</script>