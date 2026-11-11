import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps( { className: 'container lc-mdsx2026-editor-block' } );
	return (
		<div { ...blockProps }>
			<div className="lc-mdsx2026-editor-block__title">
				<span>{ __( 'Journey Nav', 'lc-mdsx2026' ) }</span>
			</div>
			<p style={ { margin: 0 } }>
				{ __( 'Sticky numbered navigation, built automatically from the stage blocks (Concept, Design, Build, Community, Opening day) on this page. Nothing to set.', 'lc-mdsx2026' ) }
			</p>
		</div>
	);
}
