import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, items } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Press Names" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<RepeaterField label={ __( "Outlets", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "name", label: "Outlet", type: "text" }, { name: "link", label: "Article (title and URL)", type: "link" } ] } emptyRow={ { name: '', link: '' } } layout="column" />
		</EditorBlockShell>
	);
}
