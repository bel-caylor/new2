const { registerBlockType } = wp.blocks;
import Edit from './edit';
import save from './save';

registerBlockType('new2/church-title-link', {
	title: 'Church Title with Link',
	description: 'Church Title & Denomination with Website Link',
	icon: 'heading',
	keywords: 'church title link',
	edit: Edit,
	save,
} );