import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import MediaField from '../../_shared/MediaField';
import VideoFields from '../../_shared/VideoFields';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, lede, crumbText, crumbUrl, filmDuration, mode } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Project Hero" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Title', 'lc-mdsx2026' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } help={ __( 'Blank = the project title.', 'lc-mdsx2026' ) } />
			<TextareaControl label={ __( 'Lede', 'lc-mdsx2026' ) } value={ lede } onChange={ ( value ) => setAttributes( { lede: value } ) } />
			<MediaField attributes={ attributes } setAttributes={ setAttributes } label={ __( 'Hero image (best finished shot; the poster in loop mode)', 'lc-mdsx2026' ) } idKey="imageId" urlKey="imageUrl" altKey="imageAlt" />
			<TextControl label={ __( 'Image alt text', 'lc-mdsx2026' ) } value={ attributes.imageAlt } onChange={ ( value ) => setAttributes( { imageAlt: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Breadcrumb text', 'lc-mdsx2026' ) } value={ crumbText } onChange={ ( value ) => setAttributes( { crumbText: value } ) } help={ __( 'Blank = "Work / " plus the first sector.', 'lc-mdsx2026' ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Breadcrumb URL', 'lc-mdsx2026' ) } value={ crumbUrl } onChange={ ( value ) => setAttributes( { crumbUrl: value } ) } /></div>
			</div>
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }>
					<SelectControl
						label={ __( 'Video mode', 'lc-mdsx2026' ) }
						value={ mode }
						options={ [ { label: __( 'Film: still image, "Watch the film" plays in place', 'lc-mdsx2026' ), value: 'film' }, { label: __( 'Loop: muted looping video as the background (media-led projects)', 'lc-mdsx2026' ), value: 'loop' } ] }
						onChange={ ( value ) => setAttributes( { mode: value } ) }
					/>
				</div>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Film length label', 'lc-mdsx2026' ) } value={ filmDuration } onChange={ ( value ) => setAttributes( { filmDuration: value } ) } help={ __( 'e.g. "1 min 54". Shown under the button.', 'lc-mdsx2026' ) } /></div>
			</div>
			<VideoFields attributes={ attributes } setAttributes={ setAttributes } />
		</EditorBlockShell>
	);
}
