import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { statement, body, ctaText, ctaUrl, facts } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Split Intro" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Statement", 'lc-mdsx2026' ) } value={ statement } onChange={ ( value ) => setAttributes( { statement: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Lead paragraph", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Lead paragraph", 'lc-mdsx2026' ) } placeholder={ __( "Lead paragraph", 'lc-mdsx2026' ) } value={ body } onChange={ ( value ) => setAttributes( { body: value } ) } />
			</div>
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Link text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Link URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
			<RepeaterField label={ __( "Facts (optional)", 'lc-mdsx2026' ) } value={ facts } onChange={ ( rows ) => setAttributes( { facts: rows } ) } fields={ [ { name: "title", label: "Title", type: "text" }, { name: "text", label: "Text", type: "text" } ] } emptyRow={ { title: '', text: '' } } layout="row" />
		</EditorBlockShell>
	);
}
