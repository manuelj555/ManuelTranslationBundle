export default function install (Vue, TranslationData) {
	Vue.prototype.getTranslationApiUrl = () => {
		return TranslationData.baseUrlApi
	}

	Vue.prototype.getLocales = () => {
		return TranslationData.locales
	}

	Vue.prototype.getDomains = () => {
		return TranslationData.domains
	}

	Vue.prototype.addDomain = (domain) => {

		if(-1 === TranslationData.domains.indexOf(domain)){
			TranslationData.domains.push(domain)
		}
	}
}