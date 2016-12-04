<template>
    <div class="btn-group dropdown-translation-domains">
        <button class="btn btn-default btn-xs" tabindex="-1">{{ domain }}</button>
        <button class="btn btn-default dropdown-toggle btn-xs" tabindex="-1" type="button" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li v-for="value in existentDomains">
                <div class="checkbox">
                    <label>
                        <input type="radio" v-model="selectedDomain"
                               :value="value"
                               @change="selectDomain($event)"
                               name="translation-domain">
                        {{ value }}
                    </label>
                </div>
            </li>
            <li>
                <input type="text" class="form-control input-sm" tabindex="-1"
                       v-model="domainInput" @change="updateDomain(true)"
                       placeholder="New Domain">
            </li>
        </ul>
    </div>
</template>

<script>
    import $ from "jquery";

    export default {
        props: {
            domain: {required: true, type: String},
            existentDomains: {required: true, type: [Array, Object]},
            onUpdate: {required: true, type: Function},
        },

        data () {
            return {
                domainInput: '',
                selectedDomain: this.domain,
            }
        },

        methods: {
            updateDomain(clear = false) {
                if (!this.domainInput.trim()) {
                    return
                }

                this.onUpdate(this.domainInput)

                if (clear) {
                    this.domainInput = ''
                }
            },
            selectDomain($event) {
//                this.onUpdate($event.target.value)
                this.onUpdate(this.selectedDomain)
            },
        },

        mounted() {
            $(this.$el).on('click', '.dropdown-menu *', (e) => {
                e.stopPropagation()
            })
        }
    }
</script>

<style>
    .dropdown-translation-domains .dropdown-menu {
        padding: 5px 10px
    }

    .dropdown-translation-domains .dropdown-menu label {
        padding-left: 0px
    }

    .dropdown-translation-domains .checkbox {
        margin: 0px
    }
</style>