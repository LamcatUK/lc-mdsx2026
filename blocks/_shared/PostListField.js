import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import PostTypePicker from './PostTypePicker';

/**
 * Ordered picklist of posts for a block attribute holding an array of post
 * IDs. Each row is a PostTypePicker with move up/down and remove controls;
 * "Add" appends an empty row. Empty rows are ignored by render templates.
 *
 * Reuses the repeater field's row/action styles (src/css/editor.css).
 *
 * @param {Object}   props
 * @param {string}   props.label    Field group label.
 * @param {string}   props.postType Post type slug to pick from.
 * @param {number[]} props.value    Current post IDs, in display order.
 * @param {Function} props.onChange ( ids: number[] ) => void
 * @param {string}   [props.help]   Optional help text.
 * @param {string}   [props.addLabel] Label for the add button.
 */
export default function PostListField( { label, postType, value, onChange, help, addLabel } ) {
	const ids = value || [];

	function update( index, id ) {
		const next = ids.slice();
		next[ index ] = id;
		onChange( next );
	}

	function move( index, direction ) {
		const target = index + direction;
		if ( target < 0 || target >= ids.length ) {
			return;
		}
		const next = ids.slice();
		[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
		onChange( next );
	}

	return (
		<div className="lc-mdsx2026-editor-field lc-mdsx2026-repeater-field">
			<label className="lc-mdsx2026-editor-field__label">{ label }</label>
			{ help && <p className="lc-mdsx2026-editor-field__help">{ help }</p> }
			<div className="lc-mdsx2026-repeater-field__rows">
				{ ids.map( ( id, index ) => (
					<div className="lc-mdsx2026-repeater-field__row" key={ index }>
						<span className="lc-mdsx2026-repeater-field__number">{ index + 1 }</span>
						<div style={ { flex: '1 1 auto' } }>
							<PostTypePicker label={ __( 'Project', 'lc-mdsx2026' ) } postType={ postType } value={ id } onChange={ ( v ) => update( index, v ) } />
						</div>
						<div className="lc-mdsx2026-repeater-field__row-actions">
							<Button size="small" label={ __( 'Move up', 'lc-mdsx2026' ) } onClick={ () => move( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'lc-mdsx2026' ) } onClick={ () => move( index, 1 ) } disabled={ index === ids.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'lc-mdsx2026' ) } onClick={ () => onChange( ids.filter( ( _id, i ) => i !== index ) ) }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
			</div>
			<Button variant="primary" onClick={ () => onChange( [ ...ids, 0 ] ) }>
				{ addLabel || __( 'Add project', 'lc-mdsx2026' ) }
			</Button>
		</div>
	);
}
