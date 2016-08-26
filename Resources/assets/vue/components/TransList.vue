<template>

<trans-item v-for="item in items" :item="item" :locales="locales"></trans-item>

<!--<table class="table table-bordered table-condensed translation-list">
    <thead>
        <tr>
            <th class="col-sm-5">Code</th>
            <th class="col-sm-7">Values</th>
        </tr>
    </thead>
    <tbody>

        {% for item in translations %}
            {{ include('@ManuelTranslation/Translation/_row.html.twig', {translation: item}) }}
        {% endfor %}
    </tbody>
</table>-->
</template>

<script>
import TransItem from './TransItem.vue'

export default {
    props: {
        items: {
            default: () => {},
        },
        locales: {required: true, type: [Array, Object]}
    },

	data () {
		return {

        }
	},

    components: {TransItem}
}
</script>