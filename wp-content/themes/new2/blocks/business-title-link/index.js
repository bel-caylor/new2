const { registerBlockType } = wp.blocks;
import Edit from './edit';
import save from './save';

registerBlockType('new2/business-title-link', {
	title: 'Business Title with Link',
	description: 'Business Title with Website Link',
	icon: 'heading',
	keywords: 'business title link',
	edit: Edit,
	save,
} );