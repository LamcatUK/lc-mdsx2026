import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, caption, quote, cite, images } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stage: Community" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Stage label', 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } help={ __( 'Blank = "Community". Use "Research" for location research.', 'lc-mdsx2026' ) } />
			<TextareaControl label={ __( 'Caption', 'lc-mdsx2026' ) } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } />
			<TextareaControl label={ __( 'Quote', 'lc-mdsx2026' ) } value={ quote } onChange={ ( value ) => setAttributes( { quote: value } ) } help={ __( 'Only real, supplied quotes. Leave blank to show images alone.', 'lc-mdsx2026' ) } />
			<TextControl label={ __( 'Quote attribution', 'lc-mdsx2026' ) } value={ cite } onChange={ ( value ) => setAttributes( { cite: value } ) } />
			<RepeaterField
				label={ __( 'Images (3: the first is the large angled crop)', 'lc-mdsx2026' ) }
				value={ images }
				onChange={ ( rows ) => setAttributes( { images: rows } ) }
				fields={ [ { name: 'image', label: 'Image', type: 'image' }, { name: 'alt', label: 'Alt text', type: 'text' } ] }
				emptyRow={ { image: 0, imageUrl: '', alt: '' } }
				layout="row"
			/>
		</EditorBlockShell>
	);
}
