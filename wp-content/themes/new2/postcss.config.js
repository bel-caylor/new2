/* eslint-env node */

module.exports = {
	plugins: [
		require('postcss-import-ext-glob'),
		require('postcss-import'),
		require('postcss-each'),
		require('tailwindcss/nesting'),
		require('tailwindcss'),
	],
};
