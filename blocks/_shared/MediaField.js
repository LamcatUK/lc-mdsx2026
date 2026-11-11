import { __ } from '@wordpress/i18n';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

/**
 * A single media-library field (image, or a video / caption file) for a block attribute pair.
 * Stores the attachment ID in `idKey` and, for images, the URL in `urlKey` (so the editor can show a
 * thumbnail). Files without a URL key store the file name in `nameKey` instead.
 *
 * @param {Object}   props
 * @param {string}   props.label        Field label.
 * @param {Object}   props.attributes   Block attributes.
 * @param {Function} props.setAttributes
 * @param {string}   props.idKey        Attribute holding the attachment ID.
 * @param {string}   [props.urlKey]     Attribute holding the URL (images).
 * @param {string}   [props.nameKey]    Attribute holding the file name (non-images).
 * @param {string}   [props.altKey]     Attribute to fill with the library alt text (images).
 * @param {string[]} [props.types]      Allowed types or MIME types, default [ 'image' ].
 * @param {string}   [props.help]       Help text.
 */
export default function MediaField( { label, attributes, setAttributes, idKey, urlKey, nameKey, altKey, types = [ 'image' ], help } ) {
	const id = attributes[ idKey ];
	const url = urlKey ? attributes[ urlKey ] : '';
	const name = nameKey ? attributes[ nameKey ] : '';

	const clear = { [ idKey ]: 0 };
	if ( urlKey ) {
		clear[ urlKey ] = '';
	}
	if ( nameKey ) {
		clear[ nameKey ] = '';
	}
	if ( altKey ) {
		clear[ altKey ] = '';
	}

	return (
		<div className="lc-mdsx2026-editor-field">
			<label className="lc-mdsx2026-editor-field__label">{ label }</label>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ ( media ) => {
						const next = { [ idKey ]: media.id };
						if ( urlKey ) {
							next[ urlKey ] = media.url;
						}
						if ( nameKey ) {
							next[ nameKey ] = media.filename || media.title || '';
						}
						if ( altKey ) {
							next[ altKey ] = media.alt || '';
						}
						setAttributes( next );
					} }
					allowedTypes={ types }
					value={ id }
					render={ ( { open } ) => (
						<div className="lc-mdsx2026-editor-field__control">
							{ url && <img src={ url } alt="" style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } } /> }
							{ name && <span style={ { display: 'block', marginBottom: '8px' } }>{ name }</span> }
							<Button variant="secondary" onClick={ open }>
								{ id ? __( 'Replace', 'lc-mdsx2026' ) : __( 'Select', 'lc-mdsx2026' ) }
							</Button>
							{ id ? (
								<Button variant="link" isDestructive onClick={ () => setAttributes( clear ) }>
									{ __( 'Remove', 'lc-mdsx2026' ) }
								</Button>
							) : null }
						</div>
					) }
				/>
			</MediaUploadCheck>
			{ help && <p className="components-base-control__help">{ help }</p> }
		</div>
	);
}
