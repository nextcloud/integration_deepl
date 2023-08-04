<template>
	<div id="deepl_prefs" class="section">
		<p>State: {{ state }}</p>
	</div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'

export default {
	name: 'AdminSettings',

	components: {
	},

	props: [],

	data() {
		return {
			state: loadState('integration_deepl', 'admin-config'),
		}
	},

	watch: {
	},

	mounted() {
	},

	methods: {
	},
}
</script>
