<!--
  - SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<span :aria-hidden="!title"
		:aria-label="title"
		class="material-design-icon"
		role="img"
		v-bind="$attrs"
		@click="$emit('click', $event)">
		<svg
			:fill="fillColor"
			:width="size"
			:height="size"
			enable-background="new 0 0 24 24"
			version="1.1"
			clip-rule="evenodd"
			fill-rule="evenodd"
			stroke-linejoin="round"
			stroke-miterlimit="2"
			viewBox="0 0 192 192"
			xml:space="preserve"
			xmlns="http://www.w3.org/2000/svg">
			<path
				clip-rule="evenodd"
				d="m523 228.804a5.999 5.999 0 0 0 -6 0l-56.869 32.833a6 6 0 0 0 -3 5.196v65.667a6 6 0 0 0 2.988 5.19l85.304 49.5a6 6 0 0 0 9.012-5.19v-29.619l25.434-14.685a5.997 5.997 0 0 0 3-5.196v-65.667a6 6 0 0 0 -3-5.196zm-3 12.124 50.869 29.37v58.738l-25.434 14.685a5.998 5.998 0 0 0 -3 5.196v22.664l-73.304-42.536v-58.747zm-10.752 72.273a11.854 11.854 0 0 0 -5.936-1.583c-6.577 0-11.917 5.34-11.917 11.917 0 6.578 5.34 11.918 11.917 11.918 6.554 0 11.88-5.303 11.917-11.848l7.762-4.463a6.004 6.004 0 0 0 2.211-8.192 6.004 6.004 0 0 0 -8.193-2.211zm-.009-24.737 21.605 12.449c.032 6.551 5.36 11.859 11.917 11.859 6.578 0 11.918-5.34 11.918-11.918 0-6.577-5.34-11.917-11.918-11.917-2.156 0-4.18.574-5.926 1.578l-21.606-12.449c-.032-6.551-5.359-11.859-11.917-11.859-6.577 0-11.917 5.34-11.917 11.918 0 6.577 5.34 11.917 11.917 11.917 2.157 0 4.18-.574 5.927-1.578z"
				fill-rule="evenodd"
				stroke-linejoin="round"
				stroke-miterlimit="2"
				transform="translate(-424 -212)" />
		</svg>
	</span>
</template>

<script>
export default {
	name: 'DeeplIcon',
	props: {
		title: {
			type: String,
			default: '',
		},
		fillColor: {
			type: String,
			default: 'currentColor',
		},
		size: {
			type: Number,
			default: 24,
		},
	},
}
</script>
