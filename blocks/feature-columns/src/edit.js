import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, marks, minWidth, items } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Feature Columns" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Intro", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Intro", 'lc-mdsx2026' ) } placeholder={ __( "Intro", 'lc-mdsx2026' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			</div>
			<SelectControl label={ __( "Marker", 'lc-mdsx2026' ) } value={ marks } options={ [{"value":"mark","label":"Coloured parallelogram"},{"value":"number","label":"Number"},{"value":"none","label":"None"}] } onChange={ ( value ) => setAttributes( { marks: value } ) } />
			<TextControl type="number" label={ __( "Minimum column width (rem)", 'lc-mdsx2026' ) } value={ minWidth } onChange={ ( value ) => setAttributes( { minWidth: Number( value ) } ) } />
			<RepeaterField label={ __( "Items", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "title", label: "Title", type: "text" }, { name: "text", label: "Text", type: "textarea" }, { name: "link", label: "Link", type: "link" } ] } emptyRow={ { title: '', text: '', link: '' } } layout="column" />
		</EditorBlockShell>
	);
}
