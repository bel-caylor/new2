module.exports = {
	content: ["./*.php", "./**/*.php"],
	//   safelist: [
	// 	  {
	// 	  	pattern: /./,
	// 	  }
	//   ],
	theme: {
		// Extend the default theme.
		container: {
			center: true,
		},
		fontFamily: {},
		fontSize: {},
	},
	plugins: [
		// Extract colors and widths from theme.json.
		require("@_tw/themejson")(require("./theme.json")),

		// Uncomment below to add additionals first-party Tailwind plugins.
		// require( '@tailwindcss/aspect-ratio' ),
		// require( '@tailwindcss/forms' ),
		// require( '@tailwindcss/line-clamp' ),
	],
};
