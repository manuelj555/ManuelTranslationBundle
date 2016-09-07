<script>
import $ from "jquery";

export default {
    params: ['time'],
    bind() {
        this.$el = $(this.el).hide()
        this.params.alertClassPrefix = this.params.alertClassPrefix || 'alert'
        
        this.vm.$on('alert-icon:show', (reference) => {
            //console.log(this.elementRef, reference)
            if(reference !== this.elementRef){
                return true;
            }

            if(this.currentTimeout){
                clearTimeout(this.currentTimeout)
            }

            this.$el.fadeIn(200)

            this.currentTimeout = setTimeout(() => {
                this.$el.fadeOut(300)
            }, this.params.time || 3000)
        })
    },
    update(value) {
        this.elementRef = value
    }
}
</script>