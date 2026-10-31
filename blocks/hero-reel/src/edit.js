import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, lede, cta1Text, cta1Url, cta2Text, cta2Url, slides, cardLabel, cardTitle, cardMeta, cardLinkText, cardLinkUrl } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Hero Reel" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Headline", 'lc-mdsx2026' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<TextareaControl label={ __( "Lede", 'lc-mdsx2026' ) } value={ lede } onChange={ ( value ) => setAttributes( { lede: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Primary button text", 'lc-mdsx2026' ) } value={ cta1Text } onChange={ ( value ) => setAttributes( { cta1Text: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Primary button URL", 'lc-mdsx2026' ) } value={ cta1Url } onChange={ ( value ) => setAttributes( { cta1Url: value } ) } /></div>
			</div>
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Secondary link text", 'lc-mdsx2026' ) } value={ cta2Text } onChange={ ( value ) => setAttributes( { cta2Text: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Secondary link URL", 'lc-mdsx2026' ) } value={ cta2Url } onChange={ ( value ) => setAttributes( { cta2Url: value } ) } /></div>
			</div>
			<RepeaterField label={ __( "Reel images (first loads eagerly; 4–8 recommended)", 'lc-mdsx2026' ) } value={ slides } onChange={ ( rows ) => setAttributes( { slides: rows } ) } fields={ [ { name: "image", label: "Image", type: "image" }, { name: "alt", label: "Alt text", type: "text" } ] } emptyRow={ { image: '', alt: '' } } layout="row" />
			<TextControl label={ __( "Card label", 'lc-mdsx2026' ) } value={ cardLabel } onChange={ ( value ) => setAttributes( { cardLabel: value } ) }
				help={ __( "Optional \"now showing\" card, bottom right.", 'lc-mdsx2026' ) } />
			<TextControl label={ __( "Card title", 'lc-mdsx2026' ) } value={ cardTitle } onChange={ ( value ) => setAttributes( { cardTitle: value } ) } />
			<TextControl label={ __( "Card meta line", 'lc-mdsx2026' ) } value={ cardMeta } onChange={ ( value ) => setAttributes( { cardMeta: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Card link text", 'lc-mdsx2026' ) } value={ cardLinkText } onChange={ ( value ) => setAttributes( { cardLinkText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Card link URL", 'lc-mdsx2026' ) } value={ cardLinkUrl } onChange={ ( value ) => setAttributes( { cardLinkUrl: value } ) } /></div>
			</div>
		</EditorBlockShell>
	);
}
