<template>
    <div>
        <!--<trans-filter :filters.sync="filters" :domains="domains"></trans-filter>-->

        <!--<div class="row paginator-container">-->
        <!--<div class="col-sm-4 total-count"><b>Items:</b> {{ totalItemsCount }}</div>-->
        <!--<div class="col-sm-8 text-right">-->
        <!--<paginator :page="currentPage" :per-page="perPage" :count="totalItemsCount", :on-click="changePage"></paginator>-->
        <!--</div>-->
        <!--</div>-->

        <!--<div v-loading="isLoading" :loading-options="{text: $t('label.loading') + '...'}">-->
        <div class="row">
            <trans-item
                    v-for="(item, index) in translationList"
                    :index="index"
                    :translation="item"
                    :locales="locales"
                    :domains="domains"
                    :changeData="change"
                    :activate="activate"
                    :deactivate="deactivate"
                    :save="save"
            ></trans-item>
        </div>
        <h3 v-if="0 === translationList.length">No Items Found!</h3>
        <!--</div>-->

        <!--<div class="text-right paginator-container">-->
        <!--<paginator :page="currentPage" :per-page="perPage" :count="totalItemsCount", :on-click="changePage"></paginator>-->
        <!--</div>-->
    </div>
</template>

<script>
    import Vue from 'vue'
    import VueResource from 'vue-resource'
    import TransItem from './TransItem.vue'
    //    import Loading from 'vue-loading'
    /*
     import TransFilter from './TransFilter.vue'
     import Paginator from './Paginator.vue'*/

    Vue.use(VueResource)

    export default {
        props: {
            apiUrl: {type: String, required: true},
            locales: {type: [Array, Object], required: true},
            domains: {type: [Array, Object], required: true},
            locale: {type: String, required: true},
        },

        data () {
            return {
                translationList: [],
                isLoading: false,
//                locales: this.getLocales(),
//                 domains: this.getDomains(),
//                 filters: {},
                totalItemsCount: 1,
                currentPage: 1,
                perPage: 50,
            }
        },

        created () {
            this.resource = this.$resource(this.apiUrl + '{/id}.json')
            this.getTranslations()
        },

        methods: {
            getTranslations () {
                this.isLoading = true;
                return this.resource.get(Object.assign({
                    page: this.currentPage,
                    perPage: this.perPage,
                }, this.filters)).then((res) => {
                    this.translationList = res.body;
                    this.isLoading = false;
                    this.totalItemsCount = parseInt(res.headers['X-Count'])
                })
            },
            activate(index) {
                this.change(index, {active: true})
            },
            deactivate(index) {
                this.change(index, {active: false})
            },
            change(index, data) {
                let translation = this.translationList[index]

                translation = Object.assign(translation, data)

                this.$set(this.translationList, index, translation)
            },
            save (index) {
                let promise = null

                let translation = this.translationList[index]

                if (!translation) {
                    return
                }

                if (translation.id) {
                    promise = this.resource.update({id: translation.id}, translation)
                } else {
                    promise = this.resource.save({}, translation)
                    promise.then(() => this.totalItemsCount++)
                }

                promise.then(response => {
                    let data = response.body
                    this.change(index, data)
//                    this.domains = this.addDomain(data.domain)
                })

                return promise
            },
        },

        components: {TransItem, /*TransFilter, Paginator*/},
//        directives: {Loading},
    }
</script>

<style>
    .paginator-container .pagination {
        margin: 0 0 5px 0;
    }

    .total-count {
        padding-top: 10px;
    }


</style>