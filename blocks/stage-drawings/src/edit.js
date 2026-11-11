import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, caption, images } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stage: Design (Drawings)" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Stage label', 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } help={ __( 'Blank = "Design".', 'lc-mdsx2026' ) } />
			<TextareaControl label={ __( 'Caption', 'lc-mdsx2026' ) } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } />
			<RepeaterField
				label={ __( 'Drawings (the first is shown large; up to 5)', 'lc-mdsx2026' ) }
				value={ images }
				onChange={ ( rows ) => setAttributes( { images: rows } ) }
				fields={ [ { name: 'image', label: 'Image', type: 'image' }, { name: 'alt', label: 'Alt text', type: 'text' }, { name: 'caption', label: 'Caption chip', type: 'text' } ] }
				emptyRow={ { image: 0, imageUrl: '', alt: '', caption: '' } }
				layout="row"
			/>
		</EditorBlockShell>
	);
}
