import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { quote, cite } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Quote" textDomain="lc-mdsx2026">
			<TextareaControl label={ __( "Quote", 'lc-mdsx2026' ) } value={ quote } onChange={ ( value ) => setAttributes( { quote: value } ) } />
			<TextControl label={ __( "Attribution", 'lc-mdsx2026' ) } value={ cite } onChange={ ( value ) => setAttributes( { cite: value } ) } />
		</EditorBlockShell>
	);
}
