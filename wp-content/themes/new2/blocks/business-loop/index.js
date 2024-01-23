const { registerBlockType } = wp.blocks;
import Edit from './edit';
import save from './save';

registerBlockType('new2/business-loop', {
	title: 'Business Loop',
	description: 'Business Query Loop',
	icon: 'megaphone',
	keywords: 'query business directory',
	attributes: {
		pageType: {
			type: "string",
		}
	},
	edit: Edit,
	save,
} );