import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import MediaField from '../../_shared/MediaField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, beforeLabel, afterLabel, beforeAlt, afterAlt } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );
	const shared = { attributes, setAttributes };

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Compare Slider" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Heading', 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<TextControl label={ __( 'Intro line', 'lc-mdsx2026' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			<div style={ { display: 'flex', gap: '24px', flexWrap: 'wrap' } }>
				<div style={ { flex: '1 1 260px' } }>
					<MediaField { ...shared } label={ __( 'Before image (left)', 'lc-mdsx2026' ) } idKey="beforeId" urlKey="beforeUrl" altKey="beforeAlt" />
					<TextControl label={ __( 'Before alt text', 'lc-mdsx2026' ) } value={ beforeAlt } onChange={ ( value ) => setAttributes( { beforeAlt: value } ) } />
					<TextControl label={ __( 'Before label', 'lc-mdsx2026' ) } value={ beforeLabel } onChange={ ( value ) => setAttributes( { beforeLabel: value } ) } />
				</div>
				<div style={ { flex: '1 1 260px' } }>
					<MediaField { ...shared } label={ __( 'After image (right)', 'lc-mdsx2026' ) } idKey="afterId" urlKey="afterUrl" altKey="afterAlt" />
					<TextControl label={ __( 'After alt text', 'lc-mdsx2026' ) } value={ afterAlt } onChange={ ( value ) => setAttributes( { afterAlt: value } ) } />
					<TextControl label={ __( 'After label', 'lc-mdsx2026' ) } value={ afterLabel } onChange={ ( value ) => setAttributes( { afterLabel: value } ) } />
				</div>
			</div>
			<p className="components-base-control__help">{ __( 'Pairs are similar subjects, not matched camera angles. Keep the labels honest (e.g. "Concept render" / "Opening day").', 'lc-mdsx2026' ) }</p>
		</EditorBlockShell>
	);
}
