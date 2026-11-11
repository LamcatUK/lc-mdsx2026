import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, caption, items } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stage: Build (Behind the scenes)" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Stage label', 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } help={ __( 'Blank = "Build". Media-led projects: "Production".', 'lc-mdsx2026' ) } />
			<TextareaControl label={ __( 'Caption', 'lc-mdsx2026' ) } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } />
			<RepeaterField
				label={ __( 'Behind the scenes (up to 12; widths and offsets are automatic)', 'lc-mdsx2026' ) }
				value={ items }
				onChange={ ( rows ) => setAttributes( { items: rows } ) }
				fields={ [
					{ name: 'image', label: 'Image (or clip poster)', type: 'image' },
					{ name: 'alt', label: 'Alt text', type: 'text' },
					{ name: 'tape', label: 'Tape label', type: 'text', help: 'e.g. In the shop, On site' },
					{ name: 'webm', label: 'Clip WebM (portrait phone clip)', type: 'file', mimeTypes: [ 'video/webm' ] },
					{ name: 'mp4', label: 'Clip MP4', type: 'file', mimeTypes: [ 'video/mp4' ] },
				] }
				emptyRow={ { image: 0, imageUrl: '', alt: '', tape: '', webm: 0, mp4: 0 } }
				layout="column"
			/>
		</EditorBlockShell>
	);
}
