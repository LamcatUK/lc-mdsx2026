import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, count, backgroundShape } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Background shape', 'lc-mdsx2026' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Parallelogram', 'lc-mdsx2026' ) }
						value={ backgroundShape }
						options={ [ { label: __( 'None', 'lc-mdsx2026' ), value: '' }, { label: __( 'From the right', 'lc-mdsx2026' ), value: 'right' }, { label: __( 'From the left', 'lc-mdsx2026' ), value: 'left' } ] }
						onChange={ ( value ) => setAttributes( { backgroundShape: value } ) }
						help={ __( 'A large, subtle brand parallelogram behind the content, entering from one side. Use it to break up neighbouring blocks with the same background.', 'lc-mdsx2026' ) }
					/>
				</PanelBody>
			</InspectorControls>
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="News List" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<TextControl type="number" label={ __( "Number of articles", 'lc-mdsx2026' ) } value={ count } onChange={ ( value ) => setAttributes( { count: Number( value ) } ) } />
		</EditorBlockShell>
		</>
	);
}
