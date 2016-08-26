<template>
<div class="row">
	<div class="col-sm-9 col-lg-10">
		<trans-item-value v-for="locale in locales" 
		:value="getValue(locale)" :locale="locale" :editing="editing"></trans-item-value>
	</div> 
	<div class="col-sm-3 col-lg-2 translation-item-actions">

		<button type="button" v-show="!editing" class="btn btn-info btn-xs" @click="editing = true">
			<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
			Edit 
		</button>

		<button type="button" v-show="!editing" class="btn btn-default btn-xs" @click="editing = true">
			<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
			Revitions
		</button>

		<button type="button" v-show="!editing" class="btn btn-warning btn-xs" @click="editing = true">
			<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span>
			Inactivate
		</button>

		<button type="button" v-show="editing" class="btn btn-primary btn-xs" @click="editing = false">Save</button>

		<button type="button" v-show="editing" class="btn btn-danger btn-xs" @click="editing = false">Cancel</button>

	</div>
</div>
</template>

<script>
import Vue from 'vue'
import TransItemValue from './TransItemValue.vue'

export default {
	props: {
		values: {
			required: true,
			type: [Object, Array],
			twoWay: true
		},
		locales: {required: true, type: [Array, Object]}
	},

	data () {
		return {
			editing: false,
		}
	},

	methods: {
		getValue (locale) {
			return this.values[locale] ? this.values[locale] : ''
		},

		updateValue (locale, value) {
			Vue.set(this.values, locale, value)
		},
	},

	events: {
		'update-value' (locale, value) {
			this.updateValue(locale, value)
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