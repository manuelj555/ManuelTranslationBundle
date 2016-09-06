<template>
<div class="btn-group dropdown-translation-domains">
    <button class="btn btn-default btn-sm" tabindex="-1">{{ domain }}</button>
    <button class="btn btn-default dropdown-toggle btn-sm" tabindex="-1" type="button" data-toggle="dropdown">
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li v-for="value in domains">
            <div class="checkbox">
                <label>
                    <input type="radio" v-model="domain" value="{{ value }}" tabindex="-1"> {{ value }}
                </label>
            </div>
        </li>
        <li>
            <input type="text" class="form-control input-sm" tabindex="-1"
            v-model="domainInput" @change="updateDomain(true)" @keyup="updateDomain()" 
            @blur="updateDomain(true)" placeholder="New Domain">
        </li>
    </ul>
</div>
</template>

<script>
export default {
    props: {
        domain: { required: true, twoWay: true },
    },

    data () {
        return {
            domainInput: '',
            domains: this.getDomains(),
        }
    },

    methods: {
        updateDomain(clear = false) {
            if(!this.domainInput.trim()){
                return 
            }
            
            this.domain = this.domainInput

            if(clear) {
                this.domainInput = ''
            }
        }
    },

    ready() {
        $(this.$el).on('click', '.dropdown-menu *', (e) => {
            e.stopPropagation()
        })
    }   
}
</script>

<style>
    .dropdown-translation-domains .dropdown-menu{padding: 5px 10px}
    .dropdown-translation-domains .dropdown-menu label{padding-left: 0px}
    .dropdown-translation-domains .checkbox{margin: 0px}
</style>