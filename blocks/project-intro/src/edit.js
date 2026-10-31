import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { surface, statement, body, meta, tags } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Project Intro" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Background", 'lc-mdsx2026' ) } value={ surface } options={ [{"value":"nero","label":"Nero (dark)"},{"value":"charcoal","label":"Charcoal"},{"value":"sea","label":"Sea Foam"},{"value":"crusta","label":"Crusta"},{"value":"solitude","label":"Solitude (light)"}] } onChange={ ( value ) => setAttributes( { surface: value } ) } />
			<TextControl label={ __( "Statement", 'lc-mdsx2026' ) } value={ statement } onChange={ ( value ) => setAttributes( { statement: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Lead paragraph", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Lead paragraph", 'lc-mdsx2026' ) } placeholder={ __( "Lead paragraph", 'lc-mdsx2026' ) } value={ body } onChange={ ( value ) => setAttributes( { body: value } ) } />
			</div>
			<RepeaterField label={ __( "Meta (client, location, year, awards…)", 'lc-mdsx2026' ) } value={ meta } onChange={ ( rows ) => setAttributes( { meta: rows } ) } fields={ [ { name: "label", label: "Label", type: "text" }, { name: "value", label: "Value", type: "text" } ] } emptyRow={ { label: '', value: '' } } layout="row" />
			<TextareaControl label={ __( "Discipline tags (one per line)", 'lc-mdsx2026' ) } value={ tags } onChange={ ( value ) => setAttributes( { tags: value } ) } />
		</EditorBlockShell>
	);
}
