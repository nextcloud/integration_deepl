<!--
  - SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div id="deepl_prefs" class="section">
		<h2>
			<DeeplIcon class="icon" />
			{{ t('integration_deepl', 'DeepL integration') }}
		</h2>
		<div id="deepl-content">
			<NcNoteCard type="info">
				{{ t('integration_deepl', 'Set an API key to start using the DeepL integration') }}
			</NcNoteCard>
			<NcTextField
				v-model="state.apikey"
				type="password"
				:label="t('integration_deepl', 'API Key')"
				:placeholder="t('integration_deepl', 'API key for DeepL')"
				:show-trailing-button="!!state.apikey"
				@update:model-value="onInput"
				@trailing-button-click="state.apikey = '' ; onInput()">
				<template #icon>
					<KeyOutlineIcon :size="20" />
				</template>
			</NcTextField>
		</div>
	</div>
</template>

<script>
import KeyOutlineIcon from 'vue-material-design-icons/KeyOutline.vue'

import DeeplIcon from './icons/DeeplIcon.vue'

import NcNoteCard from '@nextcloud/vue/components/NcNoteCard'
import NcTextField from '@nextcloud/vue/components/NcTextField'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'
import { confirmPassword } from '@nextcloud/password-confirmation'
import debounce from 'debounce'

export default {
	name: 'AdminSettings',

	components: {
		KeyOutlineIcon,
		DeeplIcon,
		NcNoteCard,
		NcTextField,
	},

	props: [],

	data() {
		return {
			readonly: true,
			state: loadState('integration_deepl', 'admin-config'),
		}
	},

	methods: {
		onInput: debounce(async function() {
			if (this.state.apikey !== 'dummyKey') {
				await this.saveOptions({
					apikey: this.state.apikey,
				})
			}
		}, 2000),
		async saveOptions(values) {
			await confirmPassword()
			const req = { values }
			const url = generateUrl('/apps/integration_deepl/admin-config')

			try {
				await axios.put(url, req)
				showSuccess(t('integration_deepl', 'DeepL admin options saved'))
			} catch (error) {
				showError(
					t('integration_deepl', 'Failed to save DeepL admin options')
					+ ': ' + (error.response?.request?.responseText ?? ''),
				)
				console.error(error)
			}
		},
	},
}
</script>

<style lang="scss" scoped>
#deepl_prefs {
	#deepl-content {
		margin-left: 40px;
		max-width: 900px;
		display: flex;
		flex-direction: column;
		gap: 4px;
		align-items: start;
	}

	h2 {
		justify-content: start;
		display: flex;
		align-items: center;
		gap: 8px;
		margin-top: 8px;
	}
}
</style>
