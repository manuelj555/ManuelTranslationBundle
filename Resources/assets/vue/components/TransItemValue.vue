<template>
	<div class="row">
		<div class="col-sm-2 translation-item-locale">{{ locale }}</div>
		<div class="col-sm-10">
			<pre v-show="!editing">{{ value }}</pre>
			<textarea class="form-control" v-show="editing" @change="updateValue()" v-model="value"></textarea>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		value: {
			required: true,
			type: String,
			twoWay: false
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

	methods: {
		updateValue () {
			this.$dispatch('update-value', this.locale, this.value)
		}
	}
}
</script>

<style>
	.translation-item-locale {
		text-align: right;
		padding-top: 8px;
		font-weight: bold;
		font-size: 1.2em;
		text-transform: uppercase;
	}
</style>