import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { crumbText, crumbUrl, name, role, photoId, photoUrl, photoAlt, bio, backgroundShape } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Background shape', 'lc-mdsx2026' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Parallelogram', 'lc-mdsx2026' ) }
						value={ backgroundShape }
						options={ [ { label: __( 'None', 'lc-mdsx2026' ), value: '' }, { label: __( 'From the right', 'lc-mdsx2026' ), value: 'right' }, { label: __( 'From the left', 'lc-mdsx2026' ), value: 'left' } ] }
						onChange={ ( value ) => setAttributes( { backgroundShape: value } ) }
						help={ __( 'A large, subtle brand parallelogram behind the content, entering from one side. Use it to break up neighbouring blocks with the same background.', 'lc-mdsx2026' ) }
					/>
				</PanelBody>
			</InspectorControls>
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Profile Hero" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Breadcrumb text", 'lc-mdsx2026' ) } value={ crumbText } onChange={ ( value ) => setAttributes( { crumbText: value } ) } />
			<TextControl label={ __( "Breadcrumb URL", 'lc-mdsx2026' ) } value={ crumbUrl } onChange={ ( value ) => setAttributes( { crumbUrl: value } ) } />
			<TextControl label={ __( "Name (blank = page title)", 'lc-mdsx2026' ) } value={ name } onChange={ ( value ) => setAttributes( { name: value } ) } />
			<TextControl label={ __( "Role", 'lc-mdsx2026' ) } value={ role } onChange={ ( value ) => setAttributes( { role: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Headshot", 'lc-mdsx2026' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => setAttributes( { photoId: media.id, photoUrl: media.url, photoAlt: media.alt || '' } ) }
						allowedTypes={ [ 'image' ] }
						value={ photoId }
						render={ ( { open } ) => (
							<div className="lc-mdsx2026-editor-field__control">
								{ photoUrl && <img src={ photoUrl } alt={ photoAlt } style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } } /> }
								<Button variant="secondary" onClick={ open }>{ photoUrl ? __( 'Replace image', 'lc-mdsx2026' ) : __( 'Select image', 'lc-mdsx2026' ) }</Button>
								{ photoUrl && <Button variant="link" isDestructive onClick={ () => setAttributes( { photoId: 0, photoUrl: '', photoAlt: '' } ) }>{ __( 'Remove', 'lc-mdsx2026' ) }</Button> }
							</div>
						) }
					/>
				</MediaUploadCheck>
				<p className="lc-mdsx2026-editor-field__help">{ __( "Use the masked PNG; it is displayed without clipping.", 'lc-mdsx2026' ) }</p>
			</div>
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Bio", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Bio", 'lc-mdsx2026' ) } placeholder={ __( "Bio", 'lc-mdsx2026' ) } value={ bio } onChange={ ( value ) => setAttributes( { bio: value } ) } />
			</div>
		</EditorBlockShell>
		</>
	);
}
