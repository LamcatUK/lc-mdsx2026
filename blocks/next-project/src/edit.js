import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

import EditorBlockShell from '../../_shared/EditorBlockShell';

import PostTypePicker from '../../_shared/PostTypePicker';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { project } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Next Project" textDomain="lc-mdsx2026">
			<PostTypePicker label={ __( "Project (optional)", 'lc-mdsx2026' ) } postType="portfolio" value={ project } onChange={ ( id ) => setAttributes( { project: id } ) } />
		</EditorBlockShell>
	);
}
