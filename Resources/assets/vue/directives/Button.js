import _ from 'lodash'

function getValue(valueOrObject) {
    return (valueOrObject instanceof Object ? valueOrObject.value : valueOrObject)
}

function getDoneText(valueOrObject) {
    return (valueOrObject instanceof Object ? valueOrObject.done : null)
}

function getButtonText($el) {
    return $el.is('input') ? $el.val() : $el.html()
}

function setButtonText($el, text) {
    return $el.is('input') ? $el.val(text) : $el.html(text)
}

function startLoading($el) {
    setButtonText($el, 'Loading...').attr('disabled', 'disabled')
}

function stopLoading($el) {
    setButtonText($el, $el.data('originalText')).attr('disabled', false)
}

export default {
    bind (el, {value}) {
        let $el = $(el), isLoading = getValue(value)
        let originalText = $el.html()

        $el.data('originalText', originalText)

        if (isLoading) {
            startLoading($el)
        }
    },
    update (el, {value, oldValue}) {
        let $el = $(el), isLoading = getValue(value), text = getDoneText(value)

        isLoading ? startLoading($el) : stopLoading($el)

        if (isLoading || !text || !getValue(oldValue)) {
            return
        }

        setButtonText($el, text)

        _.delay(() => {
            stopLoading($el)
        }, 1000)
    },
}