import _ from "lodash";

export default class Validation {
    constructor(item) {
        this.item = item
    }

    validate() {
        let errors = {}, code = _.get(this.item, 'code', '')

        if (!code.trim()) {
            _.set(errors, 'code.required', true)
        }

        if (code.trim().length > 0 && code.trim().length < 3) {
            _.set(errors, 'code.minlength', true)
        }

        return errors
    }
}