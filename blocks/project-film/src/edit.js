import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import VideoFields from '../../_shared/VideoFields';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, duration, playLabel, aspect } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Project Film" textDomain="lc-mdsx2026">
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Heading', 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Length label', 'lc-mdsx2026' ) } value={ duration } onChange={ ( value ) => setAttributes( { duration: value } ) } help={ __( 'e.g. "1 min 54"', 'lc-mdsx2026' ) } /></div>
			</div>
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( 'Play button text', 'lc-mdsx2026' ) } value={ playLabel } onChange={ ( value ) => setAttributes( { playLabel: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }>
					<SelectControl
						label={ __( 'Frame shape', 'lc-mdsx2026' ) }
						value={ aspect }
						options={ [ { label: '16:9', value: '16-9' }, { label: __( 'Ultra-wide letterbox (projection strips)', 'lc-mdsx2026' ), value: 'wide' }, { label: __( 'Portrait (phone clip)', 'lc-mdsx2026' ), value: 'portrait' } ] }
						onChange={ ( value ) => setAttributes( { aspect: value } ) }
						help={ __( 'Match the clip; do not force 16:9.', 'lc-mdsx2026' ) }
					/>
				</div>
			</div>
			<VideoFields attributes={ attributes } setAttributes={ setAttributes } />
		</EditorBlockShell>
	);
}
