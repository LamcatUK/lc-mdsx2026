import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { surface, heading, people, press } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="People Strip" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Background", 'lc-mdsx2026' ) } value={ surface } options={ [{"value":"nero","label":"Nero (dark)"},{"value":"charcoal","label":"Charcoal"},{"value":"sea","label":"Sea Foam"},{"value":"crusta","label":"Crusta"},{"value":"solitude","label":"Solitude (light)"}] } onChange={ ( value ) => setAttributes( { surface: value } ) } />
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<RepeaterField label={ __( "People", 'lc-mdsx2026' ) } value={ people } onChange={ ( rows ) => setAttributes( { people: rows } ) } fields={ [ { name: "photo", label: "Headshot", type: "image" }, { name: "name", label: "Name", type: "text" }, { name: "role", label: "Role", type: "text" }, { name: "bio", label: "Blurb", type: "textarea" }, { name: "link", label: "Profile link", type: "link" } ] } emptyRow={ { photo: '', name: '', role: '', bio: '', link: '' } } layout="column" />
			<TextareaControl label={ __( "Press / recognition names (one per line)", 'lc-mdsx2026' ) } value={ press } onChange={ ( value ) => setAttributes( { press: value } ) } />
		</EditorBlockShell>
	);
}
