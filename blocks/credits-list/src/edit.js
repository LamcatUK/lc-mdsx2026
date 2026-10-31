import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { surface, heading, items } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Credits List" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Background", 'lc-mdsx2026' ) } value={ surface } options={ [{"value":"nero","label":"Nero (dark)"},{"value":"charcoal","label":"Charcoal"},{"value":"sea","label":"Sea Foam"},{"value":"crusta","label":"Crusta"},{"value":"solitude","label":"Solitude (light)"}] } onChange={ ( value ) => setAttributes( { surface: value } ) } />
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<RepeaterField label={ __( "Credits", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "role", label: "Role", type: "text" }, { name: "name", label: "Name / company", type: "text" } ] } emptyRow={ { role: '', name: '' } } layout="row" />
		</EditorBlockShell>
	);
}
