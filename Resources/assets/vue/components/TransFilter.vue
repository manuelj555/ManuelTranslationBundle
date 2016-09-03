<template>

<div class="panel panel-default">
    <div class="panel-body">
        <form>
            <div class="row">
                <div class="col-sm-8 col-md-9">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="filter_search" class="col-sm-2 control-label">{{ $t('label.search') }}</label>
                            <div class="col-sm-10">
                                <input type="search" class="form-control" id="filter_search" placeholder="{{ $t('label.search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="col-sm-2 text-right">{{ $t('label.domains') }}</div>
                        <div class="col-sm-10">
                            <div class="checkbox" v-for="domain in domains">
                                <label>
                                    <input type="checkbox" :value="domain"> {{ domain }}
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="col-sm-2 text-right">{{ $t('label.filter_status') }}</div>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="true"> {{ $t('label.with_conflicts') }}
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="true"> {{ $t('label.with_changes') }}
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="true"> {{ $t('label.inactives') }}
                                </label>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-md-3 text-right">
                    <button type="submit" class="btn btn-info">
                        <span aria-hidden="true" class="glyphicon glyphicon-repeat"></span> {{ $t('label.apply') }}
                    </button>
                    <a class="btn btn-default" href="#">
                        <span aria-hidden="true" class="glyphicon glyphicon-repeat"></span>
                        {{ $t('label.clear') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

</template>

<script>
import Vue from 'vue'

export default {
    props: {
        domains: {required: true, type: [Object, Array]}
    },
}
</script>