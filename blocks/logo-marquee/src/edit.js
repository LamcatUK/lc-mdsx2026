import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RepeaterField from '../../_shared/RepeaterField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, layout, invert, logos } = attributes;
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'lc-mdsx2026' ) }>
					<SelectControl
						label={ __( 'Display logos as', 'lc-mdsx2026' ) }
						value={ layout }
						options={ [ { label: __( 'Scrolling marquee', 'lc-mdsx2026' ), value: 'marquee' }, { label: __( 'Grid', 'lc-mdsx2026' ), value: 'grid' } ] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
					<ToggleControl
						label={ __( 'Dark logos on a light background', 'lc-mdsx2026' ) }
						help={ __( 'Off: light logos on Nero. On: dark logos on Solitude. Either way the logo files are recoloured automatically, so supply them as dark or full-colour artwork on a transparent background.', 'lc-mdsx2026' ) }
						checked={ !! invert }
						onChange={ ( value ) => setAttributes( { invert: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Logo Marquee" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Accessible label", 'lc-mdsx2026' ) } value={ label } onChange={ ( value ) => setAttributes( { label: value } ) } />
			<RepeaterField label={ __( "Logos", 'lc-mdsx2026' ) } value={ logos } onChange={ ( rows ) => setAttributes( { logos: rows } ) } fields={ [ { name: "image", label: "Logo", type: "image" }, { name: "alt", label: "Client name", type: "text" } ] } emptyRow={ { image: '', alt: '' } } layout="row" />
		</EditorBlockShell>
		</>
	);
}
