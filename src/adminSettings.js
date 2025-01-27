/* jshint esversion: 6 */

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Vue from 'vue'
import './bootstrap.js'
import AdminSettings from './components/AdminSettings.vue'

const VueAdminSettings = Vue.extend(AdminSettings)
new VueAdminSettings().$mount('#deepl_prefs')
