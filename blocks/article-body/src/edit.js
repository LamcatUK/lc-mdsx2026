import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { imageId, imageUrl, imageAlt, caption, showDate, body } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Article Body" textDomain="lc-mdsx2026">
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Lead image", 'lc-mdsx2026' ) }</label>
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
			<TextControl label={ __( "Image caption", 'lc-mdsx2026' ) } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } />
			<ToggleControl label={ __( "Show published date", 'lc-mdsx2026' ) } checked={ showDate } onChange={ ( value ) => setAttributes( { showDate: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Article text", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Article text", 'lc-mdsx2026' ) } placeholder={ __( "Article text", 'lc-mdsx2026' ) } value={ body } onChange={ ( value ) => setAttributes( { body: value } ) } />
			</div>
		</EditorBlockShell>
	);
}
