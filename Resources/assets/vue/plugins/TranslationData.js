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

		if (TranslationData.domains instanceof Array){
			if(-1 === TranslationData.domains.indexOf(domain)){
				TranslationData.domains.push(domain)
			}
		}else if (TranslationData.domains instanceof Object) {
			if(!TranslationData.domains.hasOwnProperty(domain)){
				TranslationData.domains[domain] = domain
			}
		}
	}

	return Vue
}