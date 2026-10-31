import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, intro, address, phone, email, formId } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Contact Form" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Intro", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Intro", 'lc-mdsx2026' ) } placeholder={ __( "Intro", 'lc-mdsx2026' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			</div>
			<TextareaControl label={ __( "Address", 'lc-mdsx2026' ) } value={ address } onChange={ ( value ) => setAttributes( { address: value } ) } />
			<TextControl label={ __( "Phone", 'lc-mdsx2026' ) } value={ phone } onChange={ ( value ) => setAttributes( { phone: value } ) } />
			<TextControl label={ __( "Public email", 'lc-mdsx2026' ) } value={ email } onChange={ ( value ) => setAttributes( { email: value } ) } />
			<TextControl type="number" label={ __( "Contact Form 7 form ID", 'lc-mdsx2026' ) } value={ formId } onChange={ ( value ) => setAttributes( { formId: Number( value ) } ) }
				help={ __( "The numeric ID shown in Contact > Contact Forms (post ID in the edit URL).", 'lc-mdsx2026' ) } />
		</EditorBlockShell>
	);
}
