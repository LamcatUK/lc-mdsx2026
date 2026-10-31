import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, linkPeople, items } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Credits List" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<ToggleControl label={ __( "Link names to team bios", 'lc-mdsx2026' ) } checked={ linkPeople } onChange={ ( value ) => setAttributes( { linkPeople: value } ) }
				help={ __( "Names that exactly match a Team member link to that person's profile.", 'lc-mdsx2026' ) } />
			<RepeaterField label={ __( "Credits", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "role", label: "Role", type: "text" }, { name: "name", label: "Name / company", type: "text" } ] } emptyRow={ { role: '', name: '' } } layout="row" />
		</EditorBlockShell>
	);
}
