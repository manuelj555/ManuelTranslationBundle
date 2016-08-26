<template>
	<div class="translation-files" v-if="hasFiles()">
		<h6>Used in</h6>
		<ul>
			<li v-for="file in files">{{ file }}</li>
		</ul>
	</div>
</template>

<script>
export default {
	props: {
		files: {
			required: true,
			type: [Array, Object]
		}
	},
	methods: {
		hasFiles () {
			return this.files.length > 0
		}
	}
}
</script>

<style>
.translation-files{
	color: #aaa;
	line-height: 18px;
	font-size: 0.85em;
}
.translation-files ul{
	padding-left: 20px;
}
</style>