<template>
<div class="row">
    <div class="col-sm-9 col-md-10">
        <trans-item-value v-for="locale in locales" 
        :value.sync="values[locale]" :locale="locale" :editing="editing"></trans-item-value>
    </div> 
    <div class="col-sm-3 col-md-2 translation-item-actions">

        <button type="button" v-show="!editing" class="btn btn-info btn-xs" @click="initEdition()">
            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
            {{ $t('label.edit') }}
        </button>

        <!-- <button type="button" v-show="!editing" class="btn btn-default btn-xs" @click="editing = true">
            <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
            Revitions
        </button> -->

        <button type="button" v-show="editing" class="btn btn-primary" @click="saveValues()">{{ $t('label.save') }}</button>

        <button type="button" v-show="editing && !isNew" class="btn btn-danger btn-sm" @click="cancelEdition()">{{ $t('label.cancel') }}</button>

        <button type="button" v-show="editing && active && !isNew" class="btn btn-warning btn-xs" @click="deactivateTranslation()">
            <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span>
            {{ $t('label.deactivate') }}
        </button>

        <button type="button" v-show="editing && !active && !isNew" class="btn btn-success btn-xs" @click="activateTranslation()">
            <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span>
            {{ $t('label.activate') }}
        </button>

        <slot name="extra-buttons"></slot>

    </div>
</div>
</template>

<script>
import Vue from 'vue'
import TransItemValue from './TransItemValue.vue'
import _ from 'lodash'

export default {
    props: {
        values: {
            required: true,
            type: [Object, Array],
            twoWay: true,
        },
        locales: {required: true, type: [Array, Object]},
        editing: {required: true, type: [Boolean], twoWay: true},
        active: {required: true, type: [Boolean]},
        isNew: {required: true, type: [Boolean]},
    },

    created () {
        // establecemos los valores para los locales que no esten definidos aun.
        for(let locale of this.locales){
            this.values[locale] = this.values[locale] || ''
        }
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

        saveValues () {
            if(this.hasChanges()){
                //this.$broadcast('update-values', this.values)
                this.$dispatch('save-values', this.values)
            }else{
                this.editing = false
            }

            this.originalValues = null
        },

        initEdition () {
            this.editing = true
            this.originalValues = Object.assign({}, this.values) // clonar objeto
        },

        cancelEdition () {
            this.$broadcast('cancel-edition', this.originalValues)
            this.editing = false
            this.values = this.originalValues
            this.originalValues = null
        },

        deactivateTranslation () {
            this.$dispatch('deactivate-translation')
        },

        activateTranslation () {
            this.$dispatch('activate-translation')
        },

        hasChanges () {
            return !_.isEqual(this.values, this.originalValues) 
        },
    },

    events: {
        'activate-edition' () {
            this.initEdition()
        },
        'deactivate-edition' () {
            this.cancelEdition()
        }
    },

    components: {TransItemValue}
}
</script>

<style>
    .translation-item-actions button,
    .translation-item-actions .btn{
        margin-bottom: 10px;
        display: block; 
        width: 100%;
    }
</style>