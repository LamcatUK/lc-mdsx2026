import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, logos } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (

		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Logo Marquee" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Accessible label", 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } />
			<RepeaterField label={ __( "Logos", 'lc-mdsx2026' ) } value={ logos } onChange={ ( rows ) => setAttributes( { logos: rows } ) } fields={ [ { name: "image", label: "Logo", type: "image" }, { name: "alt", label: "Client name", type: "text" } ] } emptyRow={ { image: '', alt: '' } } layout="row" />
		</EditorBlockShell>

	);
}
