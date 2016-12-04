<template>
    <div>
        <div class="row translation-item-value-container" v-for="locale in locales">
            <div class="col-sm-2 col-md-1 translation-item-locale">{{ locale }}</div>
            <div class="col-sm-10 col-md-11">
            <pre v-if="!editing" class="translation-item-value"
                 :class="{'text-warning': isEmpty}">{{ values[locale] | defaultMessage }}</pre>
                <textarea class="form-control"
                          v-else :value="getValue(locale)"
                          @blur="updateValue(locale, $event)"
                ></textarea>
            </div>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash'

    export default {
        props: {
            values: {required: true, type: [Object, Array]},
            locales: {required: true, type: [Array, Object]},
            editing: {required: true, type: [Boolean]},
            active: {required: true, type: [Boolean]},
            onChangeData: {required: true, type: Function},
        },

        data () {
            return {
                originalValues: null,
            }
        },

        methods: {
            getValue (locale) {
                return this.values[locale] || ''
            },

            updateValue (locale, event) {
                let values = _.clone(this.values)
                values[locale] = event.target.value

                this.onChangeData({values})
            },
        },
    }
</script>

<style>
    .translation-item-actions button,
    .translation-item-actions .btn {
        margin-bottom: 10px;
        display: block;
        width: 100%;
    }
</style>