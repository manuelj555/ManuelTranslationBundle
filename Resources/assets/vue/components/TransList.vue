<template>
    <div>
        <TopProgress ref="loading"></TopProgress>

        <TransFilter :filters="filters"
                     :domains="domains"
                     :onApplyFilters="applyFilters"
        ></TransFilter>

        <div class="row paginator-container">
            <div class="col-sm-4 total-count"><b>Items:</b> {{ totalItemsCount }}</div>
            <div class="col-sm-8 text-right">
                <Paginator :page="currentPage"
                           :per-page="perPage"
                           :count="totalItemsCount"
                           :on-click="changePage"
                ></Paginator>
            </div>
        </div>

        <div class="row">
            <TransItem
                    :key="item._key"
                    v-for="(item, index) in translationList"
                    :index="index"
                    :translation="item"
                    :locales="locales"
                    :domains="existentDomains"
                    :changeData="change"
                    :activate="activate"
                    :deactivate="deactivate"
                    :save="save"
                    :remove="remove"
            ></TransItem>
        </div>
        <h3 v-if="0 === translationList.length">No Items Found!</h3>

        <div class="text-right paginator-container">
            <Paginator :page="currentPage"
                       :per-page="perPage"
                       :count="totalItemsCount"
                       :on-click="changePage"
            ></Paginator>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue'
    import VueResource from 'vue-resource'
    import TransItem from 'components/TransItem/TransItem.vue'
    import TopProgress from 'vue-top-progress'
    import Paginator from 'components/Paginator.vue'
    import TransFilter from 'components/TransFilter.vue'

    Vue.use(VueResource)

    export default {
        name: 'trans-list',
        props: {
            apiUrl: {type: String, required: true},
            locales: {type: [Array, Object], required: true},
            domains: {type: [Array, Object], required: true},
            locale: {type: String, required: true},
        },

        data () {
            return {
                translationList: [],
                filters: {
                    search: null,
                    domains: [],
                    inactive: false,
                },
                totalItemsCount: 1,
                currentPage: 1,
                perPage: 50,
                existentDomains: this.domains,
            }
        },

        created () {
            this.resource = this.$resource(this.apiUrl + '{/id}.json')
            this.getTranslations()
        },

        methods: {
            loading(action) {
                let loading = this.$refs.loading

                if (!loading) {
                    return
                }

                switch (action) {
                    case 'start':
                        loading.start()
                        break
                    case 'done':
                        loading.done()
                        break
                    case 'error':
                        loading.error()
                        break
                }
            },
            getTranslations () {
                this.loading('start');
                return this.resource.get(Object.assign({
                    page: this.currentPage,
                    perPage: this.perPage,
                }, this.filters)).then((res) => {
                    this.translationList = res.data.map(t => {
                        t._key = new Date().getTime()
                        return t
                    });
                    this.loading('done');
                    this.totalItemsCount = parseInt(res.headers.get('X-Count'))
                })
            },
            activate(index) {
                return this.change(index, {active: true}).save(index)
            },
            deactivate(index) {
                return this.change(index, {active: false}).save(index)
            },
            change(index, data) {
                let translation = Object.assign({}, this.translationList[index], data)

                this.$set(this.translationList, index, translation)

                return this
            },
            save (index) {
                let promise = null

                let translation = this.translationList[index]

                if (!translation) {
                    return
                }

                this.loading('start');

                if (translation.id) {
                    promise = this.resource.update({id: translation.id}, translation)
                } else {
                    promise = this.resource.save({}, translation)
                    promise.then(() => this.totalItemsCount++)
                }

                promise.then(response => {
                    let data = response.body
                    this.change(index, data)
                    this.loading('done');
                    this.addDomain(data.domain)
                }, () => {
                    this.loading('error');
                })

                return promise
            },
            add () {
                this.translationList.unshift({
                    _key: new Date().getTime(),
                    id: null,
                    code: '',
                    domain: 'messages',
                    values: {},
                    files: [],
                    active: true,
                    autogenerated: false,
                    'new': false,
                })
            },
            remove (index) {
                this.translationList.splice(index, 1)
            },
            addDomain (name) {
                if (this.existentDomains[name] !== undefined) {
                    return
                }

                Vue.set(this.existentDomains, name, name)
            },
            changePage(page) {
                this.currentPage = page
                this.getTranslations()
            },
            applyFilters(filters){
                this.filters = filters
                this.getTranslations()
            },
        },

        components: {TransItem, TopProgress, TransFilter, Paginator},
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