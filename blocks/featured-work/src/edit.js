import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

import PostTypePicker from '../../_shared/PostTypePicker';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, ctaText, ctaUrl, project1, project2, project3, flip } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Featured Work" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Link text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Link URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
			<PostTypePicker label={ __( "Large project", 'lc-mdsx2026' ) } postType="portfolio" value={ project1 } onChange={ ( id ) => setAttributes( { project1: id } ) } />
			<PostTypePicker label={ __( "Stacked project 1", 'lc-mdsx2026' ) } postType="portfolio" value={ project2 } onChange={ ( id ) => setAttributes( { project2: id } ) } />
			<PostTypePicker label={ __( "Stacked project 2", 'lc-mdsx2026' ) } postType="portfolio" value={ project3 } onChange={ ( id ) => setAttributes( { project3: id } ) } />
			<ToggleControl label={ __( "Large project on the right", 'lc-mdsx2026' ) } checked={ flip } onChange={ ( value ) => setAttributes( { flip: value } ) } />
		</EditorBlockShell>
	);
}
