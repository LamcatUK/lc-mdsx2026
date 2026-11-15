import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

import TermSelect from '../../_shared/TermSelect';
import PostListField from '../../_shared/PostListField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { heading, ctaText, ctaUrl, source, projects, count, discipline, backgroundShape, backgroundShapeColor } = attributes;
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
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Featured Work" textDomain="lc-mdsx2026">
			<TextControl label={ __( "Heading", 'lc-mdsx2026' ) } value={ heading } onChange={ ( value ) => setAttributes( { heading: value } ) } />
			<div style={ { display: 'flex', gap: '12px' } }>
				<div style={ { flex: '1 1 0' } }><TextControl label={ __( "Link text", 'lc-mdsx2026' ) } value={ ctaText } onChange={ ( value ) => setAttributes( { ctaText: value } ) } /></div>
				<div style={ { flex: '1 1 0' } }><TextControl type="url" label={ __( "Link URL", 'lc-mdsx2026' ) } value={ ctaUrl } onChange={ ( value ) => setAttributes( { ctaUrl: value } ) } /></div>
			</div>
			<SelectControl
				label={ __( 'Which projects', 'lc-mdsx2026' ) }
				value={ source }
				options={ [ { label: __( 'Latest projects (automatic)', 'lc-mdsx2026' ), value: 'latest' }, { label: __( 'Hand-picked', 'lc-mdsx2026' ), value: 'picked' } ] }
				onChange={ ( value ) => setAttributes( { source: value } ) }
			/>
			{ 'picked' === source ? (
				<PostListField
					label={ __( 'Projects (in order)', 'lc-mdsx2026' ) }
					postType="portfolio"
					value={ projects }
					onChange={ ( ids ) => setAttributes( { projects: ids } ) }
					help={ __( 'Shown in this order, in groups of three (one large, two stacked). Unpublished, hidden or image-less projects are skipped.', 'lc-mdsx2026' ) }
				/>
			) : (
				<>
					<TextControl type="number" label={ __( "Number of projects", 'lc-mdsx2026' ) } value={ count } onChange={ ( value ) => setAttributes( { count: Number( value ) } ) }
						help={ __( "Newest published projects first. Groups of three; use 3, 6 or 9.", 'lc-mdsx2026' ) } />
					<TermSelect label={ __( "Only show this discipline", 'lc-mdsx2026' ) } taxonomy="discipline" value={ discipline } onChange={ ( slug ) => setAttributes( { discipline: slug } ) }
						help={ __( "Leave on All for every project.", 'lc-mdsx2026' ) } />
				</>
			) }
		</EditorBlockShell>
		</>
	);
}
