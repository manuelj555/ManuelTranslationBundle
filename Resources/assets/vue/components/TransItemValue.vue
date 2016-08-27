<template>
	<div class="row">
		<div class="col-sm-2 col-md-1 translation-item-locale">{{ locale }}</div>
		<div class="col-sm-10 col-md-11">
			<pre v-show="!editing" class="translation-item-value"
			:class="{'text-warning': isEmpty}">{{ value | defaultMessage }}</pre>
			<textarea class="form-control" v-show="editing" v-model="value"></textarea>
		</div>
	</div>
</template>

<script>
import Vue from 'vue'

Vue.filter('defaultMessage', function(value) {
	return value || '[Empty Value]'
})

export default {
	props: {
		value: {
			required: true,
			type: String
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

	events: {
		'update-values' (values) {
			Vue.set(values, this.locale, this.value)
		},
		'cancel-edition' (originalValues) {
			this.value = originalValues[this.locale] || ''
		}
	}
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
</style>