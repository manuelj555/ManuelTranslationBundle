<template>
    <div class="translation-item-actions">

        <div v-show="!editing">
            <Btn type="info" size="sm" icon="edit" @click.native.prevent="onEdit">
                {{ $t('label.edit') }}
            </Btn>
        </div>
        <div v-show="editing">

            <Btn v-btn-loading="{value: saving, done: $t('label.saved')}"
                 :disabled="hasErrors"
                 type="primary"
                 icon="save"
                 @click.native.prevent="save">
                {{ $t('label.save') }}
            </Btn>

            <Btn type="danger" size="sm" icon="remove" @click.native.prevent="onCancelEdit">
                {{ $t(isNew ? 'label.remove' : 'label.cancel') }}
            </Btn>

            <Btn v-btn-loading="deactivating" v-show="(active || deactivating) && !isNew && !activating"
                 type="warning"
                 size="xs" icon="ban-circle" @click.native.prevent="deactivate">
                {{ $t('label.deactivate') }}
            </Btn>

            <Btn v-btn-loading="activating" v-show="(!active || activating) && !isNew && !deactivating"
                 type="success"
                 size="xs" icon="ok" @click.native.prevent="activate">
                {{ $t('label.activate') }}
            </Btn>

        </div>
        <slot></slot>
    </div>
</template>

<script>
    import _ from 'lodash'
    import Btn from 'components/Generic/Button.vue'
    import BtnLoading from 'directives/Button'

    export default {
        props: {
            hasErrors: {required: true, type: [Boolean]},
            editing: {required: true, type: [Boolean]},
            active: {required: true, type: [Boolean]},
            isNew: {required: true, type: [Boolean]},
            onSave: {required: true, type: Function},
            onEdit: {required: true, type: Function},
            onCancelEdit: {required: true, type: Function},
            onActivate: {required: true, type: Function},
            onDeactivate: {required: true, type: Function},
        },
        data() {
            return {
                saving: false,
                activating: false,
                deactivating: false,
            }
        },
        methods: {
            save () {
                this.saving = true
                this.onSave().then(() => {
                    this.saving = false
                }, () => {
                    this.saving = false
                })
            },
            activate () {
                this.activating = true
                this.onActivate().then(() => {
                    this.activating = false
                })
            },
            deactivate () {
                this.deactivating = true
                this.onDeactivate().then(() => {
                    this.deactivating = false
                })
            },
        },
        components: {Btn},
        directives: {BtnLoading},
    }
</script>

<style>
    .translation-item-actions button,
    .translation-item-actions .btn {
        margin-bottom: 10px;
        display: block;
        width: 100%;
    }
</style>