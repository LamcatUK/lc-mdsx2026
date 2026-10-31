import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, items, backgroundShape } = attributes;
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
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="FAQ" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<RepeaterField label={ __( "Questions", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "question", label: "Question", type: "text" }, { name: "answer", label: "Answer", type: "textarea" } ] } emptyRow={ { question: '', answer: '' } } layout="column" />
		</EditorBlockShell>
		</>
	);
}
