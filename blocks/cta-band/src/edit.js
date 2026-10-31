import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { surface, heading, ctaText, ctaUrl } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="CTA Band" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Background", 'lc-mdsx2026' ) } value={ surface } options={ [{"value":"nero","label":"Nero (dark)"},{"value":"charcoal","label":"Charcoal"},{"value":"sea","label":"Sea Foam"},{"value":"crusta","label":"Crusta"},{"value":"solitude","label":"Solitude (light)"}] } onChange={ ( value ) => setAttributes( { surface: value } ) } />
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Button text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Button URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
		</EditorBlockShell>
	);
}
