import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import MediaField from '../../_shared/MediaField';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, caption, state, openingDate, upcomingLine, ctaText, ctaUrl, cineLine, reelHeading, reel, mosaic } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stage: Opening day" textDomain="lc-mdsx2026">
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Stage label', 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } help={ __( 'Blank = "Opening day". Media-led projects: "Show".', 'lc-mdsx2026' ) } /></div>
				<div style={ { flex: '1 1 0' } }>
					<SelectControl
						label={ __( 'State', 'lc-mdsx2026' ) }
						value={ state }
						options={ [ { label: __( 'Open: show the opening-day images', 'lc-mdsx2026' ), value: 'open' }, { label: __( 'Upcoming: show an "Opening [date]" panel (in development)', 'lc-mdsx2026' ) , value: 'upcoming' } ] }
						onChange={ ( value ) => setAttributes( { state: value } ) }
					/>
				</div>
			</div>
			<TextareaControl label={ __( 'Caption', 'lc-mdsx2026' ) } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } />
			{ 'upcoming' === state ? (
				<>
					<TextControl label={ __( 'Opening date', 'lc-mdsx2026' ) } value={ openingDate } onChange={ ( value ) => setAttributes( { openingDate: value } ) } help={ __( 'Shown as "Opening [date]" and in the Journey Nav. Never guess a date.', 'lc-mdsx2026' ) } />
					<TextControl label={ __( 'One line', 'lc-mdsx2026' ) } value={ upcomingLine } onChange={ ( value ) => setAttributes( { upcomingLine: value } ) } />
					<div style={ { display: 'flex', gap: '12px' } }>
						<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Button text (optional)', 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
						<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( 'Button URL', 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
					</div>
				</>
			) : (
				<>
					<MediaField attributes={ attributes } setAttributes={ setAttributes } label={ __( 'Cinematic image (full-bleed, light parallax)', 'lc-mdsx2026' ) } idKey="cineId" urlKey="cineUrl" altKey="cineAlt" />
					<div style={ { display: 'flex', gap: '12px' } }>
						<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Cinematic image alt text', 'lc-mdsx2026' ) } value={ attributes.cineAlt } onChange={ ( value ) => setAttributes( { cineAlt: value } ) } /></div>
						<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'One line over the image', 'lc-mdsx2026' ) } value={ cineLine } onChange={ ( value ) => setAttributes( { cineLine: value } ) } /></div>
					</div>
					<TextControl label={ __( 'Reel heading', 'lc-mdsx2026' ) } value={ reelHeading } onChange={ ( value ) => setAttributes( { reelHeading: value } ) } />
					<RepeaterField
						label={ __( 'Reel (stills and portrait phone clips, up to 8)', 'lc-mdsx2026' ) }
						value={ reel }
						onChange={ ( rows ) => setAttributes( { reel: rows } ) }
						fields={ [
							{ name: 'image', label: 'Image (or clip poster)', type: 'image' },
							{ name: 'alt', label: 'Alt text', type: 'text' },
							{ name: 'webm', label: 'Clip WebM', type: 'file', mimeTypes: [ 'video/webm' ] },
							{ name: 'mp4', label: 'Clip MP4', type: 'file', mimeTypes: [ 'video/mp4' ] },
							{ name: 'tape', label: 'Clip tape label (default Live)', type: 'text' },
						] }
						emptyRow={ { image: 0, imageUrl: '', alt: '', webm: 0, mp4: 0, tape: '' } }
						layout="column"
					/>
					<RepeaterField
						label={ __( 'Mosaic (up to 8; sizes are automatic, event photos go last)', 'lc-mdsx2026' ) }
						value={ mosaic }
						onChange={ ( rows ) => setAttributes( { mosaic: rows } ) }
						fields={ [ { name: 'image', label: 'Image', type: 'image' }, { name: 'alt', label: 'Alt text', type: 'text' } ] }
						emptyRow={ { image: 0, imageUrl: '', alt: '' } }
						layout="row"
					/>
				</>
			) }
		</EditorBlockShell>
	);
}
