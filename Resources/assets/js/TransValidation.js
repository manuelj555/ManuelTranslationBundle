import _ from 'lodash'

export default class Validation 
{
    constructor (item) {
        this.item = item
        this.errors = {}
    }

    validate () {
        this.errors = {}

        if (!this.item.code || this.item.code.trim().length === 0){
            this.errors['code'] = this.errors['code'] || {}
            this.errors['code']['required'] = true
        }

        if (this.item.code && this.item.code.trim().length < 3){
            this.errors['code'] = this.errors['code'] || {}
            this.errors['code']['minlength'] = true
        }

        return _.isEmpty(this.errors)
    }

    getErrors (name = null) {
        if(name){
            return this.errors[name] || {}
        }else{
            return this.errors
        }
    }
}