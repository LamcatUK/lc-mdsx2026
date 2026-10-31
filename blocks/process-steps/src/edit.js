import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { surface, heading, intro, steps } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Process Steps" textDomain="lc-mdsx2026">
			<SelectControl label={ __( "Background", 'lc-mdsx2026' ) } value={ surface } options={ [{"value":"nero","label":"Nero (dark)"},{"value":"charcoal","label":"Charcoal"},{"value":"sea","label":"Sea Foam"},{"value":"crusta","label":"Crusta"},{"value":"solitude","label":"Solitude (light)"}] } onChange={ ( value ) => setAttributes( { surface: value } ) } />
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Intro", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Intro", 'lc-mdsx2026' ) } placeholder={ __( "Intro", 'lc-mdsx2026' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			</div>
			<RepeaterField label={ __( "Steps", 'lc-mdsx2026' ) } value={ steps } onChange={ ( rows ) => setAttributes( { steps: rows } ) } fields={ [ { name: "title", label: "Title", type: "text" }, { name: "text", label: "Text", type: "textarea" } ] } emptyRow={ { title: '', text: '' } } layout="column" />
		</EditorBlockShell>
	);
}
