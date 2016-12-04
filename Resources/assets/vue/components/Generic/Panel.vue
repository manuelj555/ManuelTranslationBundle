<template>
    <div :class="'panel panel-' + type">
        <slot name="header"></slot>
        <div class="panel-body">
            <slot name="body"></slot>
        </div>
        <slot name="footer"></slot>
    </div>
</template>

<script>
    export default {
        props: {
            type: {type: String, default: 'default'},
        },

        mounted(){
            if (this.$slots.header){
                this.$slots.header[0].elm.classList.add('panel-heading')
            }

            if (this.$slots.footer){
                this.$slots.footer[0].elm.classList.add('panel-footer')
            }
        },
    }
</script>