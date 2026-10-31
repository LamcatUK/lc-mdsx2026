import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, SelectControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { variant, crumbText, crumbUrl, title, intro, imageId, imageUrl, imageAlt, ctaText, ctaUrl } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Page Hero" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Variant", 'lc-mdsx2026' ) } value={ variant } options={ [{"value":"title","label":"Title only"},{"value":"image","label":"Full-bleed image"},{"value":"split","label":"Split (copy + image)"}] } onChange={ ( value ) => setAttributes( { variant: value } ) } />
			<TextControl label={ __( "Breadcrumb text", 'lc-mdsx2026' ) } value={ crumbText } onChange={ ( value ) => setAttributes( { crumbText: value } ) }
				help={ __( "e.g. \"Services\". Leave blank for none.", 'lc-mdsx2026' ) } />
			<TextControl label={ __( "Breadcrumb URL", 'lc-mdsx2026' ) } value={ crumbUrl } onChange={ ( value ) => setAttributes( { crumbUrl: value } ) } />
			<TextControl label={ __( "Title (blank = page title)", 'lc-mdsx2026' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Intro", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Intro", 'lc-mdsx2026' ) } placeholder={ __( "Intro", 'lc-mdsx2026' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			</div>
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Image", 'lc-mdsx2026' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => setAttributes( { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' } ) }
						allowedTypes={ [ 'image' ] }
						value={ imageId }
						render={ ( { open } ) => (
							<div className="lc-mdsx2026-editor-field__control">
								{ imageUrl && <img src={ imageUrl } alt={ imageAlt } style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } } /> }
								<Button variant="secondary" onClick={ open }>{ imageUrl ? __( 'Replace image', 'lc-mdsx2026' ) : __( 'Select image', 'lc-mdsx2026' ) }</Button>
								{ imageUrl && <Button variant="link" isDestructive onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) }>{ __( 'Remove', 'lc-mdsx2026' ) }</Button> }
							</div>
						) }
					/>
				</MediaUploadCheck>
			</div>
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Button text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Button URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
		</EditorBlockShell>
	);
}
