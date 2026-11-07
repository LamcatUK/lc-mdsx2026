import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { statement, body, meta, tags, backgroundShape, backgroundShapeColor } = attributes;
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
					<SelectControl
						label={ __( 'Shape colour', 'lc-mdsx2026' ) }
						value={ backgroundShapeColor }
						options={ [ { label: __( 'Default', 'lc-mdsx2026' ), value: '' }, { label: __( 'Sea Foam, subtle', 'lc-mdsx2026' ), value: 'sea' }, { label: __( 'Crusta, subtle', 'lc-mdsx2026' ), value: 'crusta' } ] }
						onChange={ ( value ) => setAttributes( { backgroundShapeColor: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Project Intro" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Statement", 'lc-mdsx2026' ) } value={ statement } onChange={ ( value ) => setAttributes( { statement: value } ) } />
			<div className="lc-mdsx2026-editor-field">
				<label className="lc-mdsx2026-editor-field__label">{ __( "Lead paragraph", 'lc-mdsx2026' ) }</label>
				<RichText tagName="div" className="lc-mdsx2026-editor-field__control" aria-label={ __( "Lead paragraph", 'lc-mdsx2026' ) } placeholder={ __( "Lead paragraph", 'lc-mdsx2026' ) } value={ body } onChange={ ( value ) => setAttributes( { body: value } ) } />
			</div>
			<RepeaterField label={ __( "Meta (client, location, year, awards…)", 'lc-mdsx2026' ) } value={ meta } onChange={ ( rows ) => setAttributes( { meta: rows } ) } fields={ [ { name: "label", label: "Label", type: "text" }, { name: "value", label: "Value", type: "text" } ] } emptyRow={ { label: '', value: '' } } layout="row" />
			<TextareaControl label={ __( "Discipline tags (one per line)", 'lc-mdsx2026' ) } value={ tags } onChange={ ( value ) => setAttributes( { tags: value } ) } />
		</EditorBlockShell>
		</>
	);
}
