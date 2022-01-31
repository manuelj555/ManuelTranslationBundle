<template>
    <div class="translation-files" v-if="hasFiles()">
        <div class="row">
            <div class="col-sm-2 col-md-1 translation-item-locale"><h6>Used in</h6></div>
            <div class="col-sm-10 col-md-11">
                <ul>
                    <li v-for="file in files">{{ file }}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        files: {
            required: true,
            type: [Array, Object]
        },
    },
    methods: {
        hasFiles () {
            return this.files.length > 0
        }
    }
}
</script>

<style>
.translation-files{
    color: #aaa;
    line-height: 18px;
    font-size: 0.85em;
}
.translation-files ul{
    padding-left: 20px;
    margin-top: 12px;
}
</style>