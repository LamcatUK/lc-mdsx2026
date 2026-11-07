import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, linkPeople, items, backgroundShape, backgroundShapeColor } = attributes;
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
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Credits List" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<ToggleControl label={ __( "Link names to team bios", 'lc-mdsx2026' ) } checked={ linkPeople } onChange={ ( value ) => setAttributes( { linkPeople: value } ) }
				help={ __( "Names that exactly match a Team member link to that person's profile.", 'lc-mdsx2026' ) } />
			<RepeaterField label={ __( "Credits", 'lc-mdsx2026' ) } value={ items } onChange={ ( rows ) => setAttributes( { items: rows } ) } fields={ [ { name: "role", label: "Role", type: "text" }, { name: "name", label: "Name / company", type: "text" } ] } emptyRow={ { role: '', name: '' } } layout="row" />
		</EditorBlockShell>
		</>
	);
}
