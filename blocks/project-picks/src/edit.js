import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

import PostListField from '../../_shared/PostListField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, projects } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Project Picks" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<PostListField label={ __( "Projects (in order)", 'lc-mdsx2026' ) } postType="portfolio" value={ projects } onChange={ ( ids ) => setAttributes( { projects: ids } ) } />
		</EditorBlockShell>
	);
}
