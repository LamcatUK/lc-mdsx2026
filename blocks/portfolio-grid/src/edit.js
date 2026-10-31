import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { showFilters } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Portfolio Grid" textDomain="lc-mdsx2026">
			<ToggleControl label={ __( "Show filters", 'lc-mdsx2026' ) } checked={ showFilters } onChange={ ( value ) => setAttributes( { showFilters: value } ) } />
		</EditorBlockShell>
	);
}
