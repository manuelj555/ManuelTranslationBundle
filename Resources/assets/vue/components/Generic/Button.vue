<template>
    <button :type="buttonType"
            :class="buttonClass"
    >
        <span v-if="icon !== false" :class="iconClass" aria-hidden="true"></span>
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            buttonType: {type: String, default: 'button'},
            type: {type: String, default: 'default'},
            icon: {type: [String, Boolean], default: false},
            size: {type: String, default: null},
        },
        computed: {
            buttonClass () {
                let size = (this.size ? 'btn-' + this.size : null)

                return ('btn btn-' + this.type + ' ' + size).trim()
            },

            iconClass () {
                return ('glyphicon glyphicon-'+this.icon)
            }
        },
    }
</script>