import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

import PostListField from '../../_shared/PostListField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, lede, cta1Text, cta1Url, cta2Text, cta2Url, projects, showCard, cardLabel } = attributes;
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
			<PostListField label={ __( "Projects in the reel (in order)", 'lc-mdsx2026' ) } postType="portfolio" value={ projects } onChange={ ( ids ) => setAttributes( { projects: ids } ) }
				help={ __( "Each project shows its featured image. First loads eagerly; 4-8 recommended.", 'lc-mdsx2026' ) } />
			<ToggleControl label={ __( "Show \"now showing\" card", 'lc-mdsx2026' ) } checked={ showCard } onChange={ ( value ) => setAttributes( { showCard: value } ) } />
			<TextControl label={ __( "Card label", 'lc-mdsx2026' ) } value={ cardLabel } onChange={ ( value ) => setAttributes( { cardLabel: value } ) } />
		</EditorBlockShell>
	);
}
