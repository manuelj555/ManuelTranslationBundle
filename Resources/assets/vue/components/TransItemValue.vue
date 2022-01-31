<template>
    <div class="row translation-item-value-container">
        <div class="col-sm-2 col-md-1 translation-item-locale">{{ locale }}</div>
        <div class="col-sm-10 col-md-11">
            <pre v-if="!editing" class="translation-item-value"
            :class="{'text-warning': isEmpty}">{{ value | defaultMessage }}</pre>
            <textarea class="form-control" v-else v-model="value"></textarea>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'

export default {
    props: {
        value: {
            required: true,
            type: String,
            twoWay: true
        },
        locale: {
            required: true,
            type: String
        },
        editing: {
            required: true,
            type: Boolean
        }
    },

    computed: {
        isEmpty () {
            return this.value.trim().length == 0
        }
    },

    /*events: {
        /*'cancel-edition' (originalValues) {
            this.value = originalValues[this.locale] || ''
        }* /
    }*/
}
</script>

<style>
    .translation-item-locale { 
        text-align: right;
        padding-top: 4px;
        font-weight: bold;
        font-size: 1em;
        text-transform: uppercase;
    }

    .translation-item-value{
        padding: 4px;
    }
    .translation-item-value-container textarea{
        margin-bottom: 5px;
    }
</style>