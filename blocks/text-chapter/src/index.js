import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import Edit from './edit';
import metadata from '../block.json';

registerBlockType( metadata.name, {
	edit: Edit,
	// The chapter's copy is inner blocks (paragraphs, headings, lists), rendered by render.php as $content.
	save: () => <InnerBlocks.Content />,
} );
