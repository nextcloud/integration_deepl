<?php

/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

$appId = OCA\IntegrationDeepl\AppInfo\Application::APP_ID;
\OCP\Util::addScript($appId, $appId . '-adminSettings');
?>

<div id="deepl_prefs"></div>
