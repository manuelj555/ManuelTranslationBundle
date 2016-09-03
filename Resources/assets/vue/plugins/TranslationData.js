export default function plugin (Vue, options = {}) {
	Vue.prototype.getTranslationApiUrl = function () {
		return options.baseUrlApi
	}

	Vue.prototype.getLocales = function () {
		return TranslationData.locales
	}

	Vue.prototype.getDomains = function () {
		return TranslationData.domains
	}

	Vue.prototype.addDomain = (domain) => {

		if(-1 === TranslationData.domains.indexOf(domain)){
			TranslationData.domains.push(domain)
		}
	}

	return Vue
}