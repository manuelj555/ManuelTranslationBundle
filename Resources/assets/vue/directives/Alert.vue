<script>
export default {
	params: ['time', 'alertClassPrefix'],
	twoWay: true,
	bind() {
		this.$el = $(this.el).addClass('vuejs-alert').hide()
		this.params.alertClassPrefix = this.params.alertClassPrefix || 'alert'
	},

	update (messageObj) {
		if(!messageObj.message || !messageObj.message.trim()){
			return
		}

		if(this.currentTimeout){
			clearTimeout(this.currentTimeout)
		}

		this.$el
		.clearQueue()
		.html(messageObj.message)
		.fadeIn(200)
		.addClass(this.params.alertClassPrefix + '-' + messageObj.type)

		this.currentTimeout = setTimeout(() => {
			messageObj = null
			this.$el.fadeOut(300, () => this.$el.html(''))
		}, messageObj.time || 3000)
	},
}
</script>

<style>
	.page-header h1 .vuejs-alert{
		font-size: 0.5em;
		vertical-align: middle;
	}
</style>