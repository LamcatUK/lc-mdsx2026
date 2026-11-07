import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, people, backgroundShape, backgroundShapeColor } = attributes;
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
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="People Strip" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<RepeaterField label={ __( "People", 'lc-mdsx2026' ) } value={ people } onChange={ ( rows ) => setAttributes( { people: rows } ) } fields={ [ { name: "photo", label: "Headshot", type: "image" }, { name: "name", label: "Name", type: "text" }, { name: "role", label: "Role", type: "text" }, { name: "bio", label: "Blurb", type: "textarea" }, { name: "link", label: "Profile link", type: "link" } ] } emptyRow={ { photo: '', name: '', role: '', bio: '', link: '' } } layout="column" />
		</EditorBlockShell>
		</>
	);
}
