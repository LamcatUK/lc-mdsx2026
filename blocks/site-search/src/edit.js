import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { placeholder } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Site Search" textDomain="lc-mdsx2026">
			<TextControl label={ __( 'Placeholder', 'lc-mdsx2026' ) } value={ placeholder } onChange={ ( value ) => setAttributes( { placeholder: value } ) } help={ __( 'The search field and live results appear here on the page.', 'lc-mdsx2026' ) } />
		</EditorBlockShell>
	);
}
