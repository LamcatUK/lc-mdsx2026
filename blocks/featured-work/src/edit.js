import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

import TermSelect from '../../_shared/TermSelect';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, ctaText, ctaUrl, count, discipline } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Featured Work" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Link text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Link URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
			<TextControl type="number" label={ __( "Number of projects", 'lc-mdsx2026' ) } value={ count } onChange={ ( value ) => setAttributes( { count: Number( value ) } ) }
				help={ __( "Newest published projects first. Groups of three; use 3, 6 or 9.", 'lc-mdsx2026' ) } />
			<TermSelect label={ __( "Only show this discipline", 'lc-mdsx2026' ) } taxonomy="discipline" value={ discipline } onChange={ ( slug ) => setAttributes( { discipline: slug } ) }
				help={ __( "Leave on All for every project.", 'lc-mdsx2026' ) } />
		</EditorBlockShell>
	);
}
