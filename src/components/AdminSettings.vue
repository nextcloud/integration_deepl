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
		<p class="settings-hint">
			<span>
				{{ t('integration_deepl', 'Set an API key to start using the DeepL integration') }}
			</span>
		</p>
		<div id="deepl-content">
			<div class="line">
				<label for="deepl-apikey">
					<KeyOutlineIcon :size="20" class="icon" />
					{{ t('integration_deepl', 'API Key') }}
				</label>
				<input id="deepl-apikey"
					v-model="state.apikey"
					type="password"
					:readonly="readonly"
					:placeholder="t('integration_deepl', 'API key for DeepL')"
					@input="onInput"
					@focus="readonly = false">
			</div>
		</div>
	</div>
</template>

<script>
import KeyOutlineIcon from 'vue-material-design-icons/KeyOutline.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'
import { confirmPassword } from '@nextcloud/password-confirmation'
import DeeplIcon from './icons/DeeplIcon.vue'

let timeout
const debounce = (fn, ms = 2000) => {
	clearTimeout(timeout)
	timeout = setTimeout(fn, ms)
}

export default {
	name: 'AdminSettings',

	components: {
		KeyOutlineIcon,
		DeeplIcon,
	},

	props: [],

	data() {
		return {
			readonly: true,
			state: loadState('integration_deepl', 'admin-config'),
		}
	},

	methods: {
		onInput() {
			debounce(async () => {
				if (this.state.apikey !== 'dummyKey') {
					await this.saveOptions({
						apikey: this.state.apikey,
					})
				}
			})
		},
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
	}

	h2,
	.line,
	.settings-hint {
		display: flex;
		align-items: center;

		.icon {
			margin-right: 4px;
		}
	}

	.line {
		> label {
			width: 300px;
			display: flex;
			align-items: center;
		}
		> input {
			width: 300px;
		}
	}
}
</style>
