// Set the Preflight flag based on the build target.
// const includePreflight = 'editor' === process.env._TW_TARGET ? false : true;
let includePreflight, typographyClassName;
if ('editor' === process.env._TW_TARGET) {
	includePreflight = true;
	typographyClassName = 'prose';
} else {
	includePreflight = true;
	typographyClassName = 'prose';
}

module.exports = {
	presets: [
		// Manage Tailwind Typography's configuration in a separate file.
		require('./tailwind-typography.config.js'),
	],
	content: [
		// Ensure changes to PHP files and `theme.json` trigger a rebuild.
		'./theme/**/*.php',
		'./theme/theme.json',
	],
	safelist: [
		'sticky',
		'top-0',
		'top-20'
    ],
	theme: {
		// Extend the default Tailwind theme.
		fontFamily: {
			sans: ["var(--wp--preset--font-family--sans)"],
			serif: ["var(--wp--preset--font-family--serif)"],
			script: ["var(--wp--preset--font-family--script)"],
		},
		fontSize: {
		    xs: ["var(--wp--preset--font-size--x-small)", "var(--line-height--x-small)"],
			sm: ["var(--wp--preset--font-size--small)", "var(--line-height--small)"],
			base: ["var(--wp--preset--font-size--medium)", "var(--line-height--medium)"],
			lg: ["var(--wp--preset--font-size--large)", "var(--line-height--large)"],
			xl: ["var(--wp--preset--font-size--x-large)", "var(--line-height--x-large)"],
			"2xl": ["var(--wp--preset--font-size--2-xl)", "var(--line-height--2-xl)"],
			"3xl": ["var(--wp--preset--font-size--3-xl)", "var(--line-height--3-xl)"],
			"4xl": ["var(--wp--preset--font-size--4-xl)", "var(--line-height--4-xl)"],
			"5xl": ["var(--wp--preset--font-size--5-xl)", "var(--line-height--5-xl)"],
			"6xl": ["var(--wp--preset--font-size--6-xl)", "var(--line-height--6-xl)"],
			"7xl": ["var(--wp--preset--font-size--7-xl)", "var(--line-height--7-xl)"],
			"8xl": ["var(--wp--preset--font-size--8-xl)", "var(--line-height--8-xl)"],
			"9xl": ["var(--wp--preset--font-size--9-xl)", "var(--line-height--9-xl)"],
		}
	},
	corePlugins: {
		// Disable Preflight base styles in builds targeting the editor.
		preflight: includePreflight,
	},
	plugins: [
		// Extract colors and widths from `theme.json`.
		require('@_tw/themejson')(require('../theme/theme.json')),

		// Add Tailwind Typography.
		require('@tailwindcss/typography'),

		// Uncomment below to add additional first-party Tailwind plugins.
		// require('@tailwindcss/forms'),
		// require('@tailwindcss/aspect-ratio'),
		// require('@tailwindcss/container-queries'),
	],
};
