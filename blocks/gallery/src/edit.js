import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { layout, images } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Gallery Row" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Layout", 'lc-mdsx2026' ) } value={ layout } options={ [{"value":"three","label":"3-up"},{"value":"two","label":"2-up (wide + narrow)"},{"value":"full","label":"Full width"}] } onChange={ ( value ) => setAttributes( { layout: value } ) } />
			<RepeaterField label={ __( "Images", 'lc-mdsx2026' ) } value={ images } onChange={ ( rows ) => setAttributes( { images: rows } ) } fields={ [ { name: "image", label: "Image", type: "image" }, { name: "alt", label: "Alt text", type: "text" } ] } emptyRow={ { image: '', alt: '' } } layout="row" />
		</EditorBlockShell>
	);
}
