<template>
<div class="translation-item">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ item.code }}</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-4">
					<trans-item-files :files="item.files"></trans-item-files>
					{{ item.values | json }}
				</div>
				<div class="col-sm-8">
					<trans-item-values :values.sync="item.values" :locales="locales"></trans-item-values>
				</div>
			</div>
		</div>
	</div>
	 <!-- {{ item|json }}	 -->
</div>
</template>

<script>
import TransItemValues from './TransItemValues.vue'
import TransItemFiles from './TransItemFiles.vue'
//import TransItemValue from './TransItemValue.vue'

export default {
	props: {
		item: {
			required: true,
			type: Object
		},
		locales: {required: true, type: [Array, Object]}
	},

	data () {
		return {}
	},

	components: {TransItemValues, TransItemFiles}
}
</script>