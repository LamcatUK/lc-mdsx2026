import { __ } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';
import MediaField from './MediaField';

/**
 * The shared video attributes (hero, film): WebM + MP4 files, optional Vimeo ID, poster image, optional
 * VTT captions. Attribute names: webm, webmName, mp4, mp4Name, vimeoId, poster, posterUrl, captions, captionsName.
 * (Add `mode` yourself where the block offers loop vs film.)
 *
 * @param {Object}   props
 * @param {Object}   props.attributes
 * @param {Function} props.setAttributes
 */
export default function VideoFields( { attributes, setAttributes } ) {
	const shared = { attributes, setAttributes };
	return (
		<>
			<div style={ { display: 'flex', gap: '24px', flexWrap: 'wrap' } }>
				<MediaField { ...shared } label={ __( 'Video, WebM', 'lc-mdsx2026' ) } idKey="webm" nameKey="webmName" types={ [ 'video/webm' ] } />
				<MediaField { ...shared } label={ __( 'Video, MP4', 'lc-mdsx2026' ) } idKey="mp4" nameKey="mp4Name" types={ [ 'video/mp4' ] } />
				<MediaField { ...shared } label={ __( 'Poster image', 'lc-mdsx2026' ) } idKey="poster" urlKey="posterUrl" />
			</div>
			<div style={ { display: 'flex', gap: '24px', flexWrap: 'wrap', alignItems: 'flex-end' } }>
				<div style={ { flex: '1 1 240px' } }>
					<TextControl
						label={ __( 'Vimeo ID (optional)', 'lc-mdsx2026' ) }
						value={ attributes.vimeoId }
						onChange={ ( value ) => setAttributes( { vimeoId: value.replace( /\D/g, '' ) } ) }
						help={ __( 'Films only. Loads Vimeo after the play click; the uploaded files are used if this is blank.', 'lc-mdsx2026' ) }
					/>
				</div>
				<MediaField { ...shared } label={ __( 'Captions (VTT, optional)', 'lc-mdsx2026' ) } idKey="captions" nameKey="captionsName" types={ [ 'text/vtt' ] } />
			</div>
		</>
	);
}
