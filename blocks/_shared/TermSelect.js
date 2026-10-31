import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Select of a taxonomy's terms for a block attribute holding one term slug
 * ('' = all). Backed by @wordpress/core-data, so new terms appear without
 * code changes.
 *
 * @param {Object}   props
 * @param {string}   props.label     Field label.
 * @param {string}   props.taxonomy  Taxonomy slug (e.g. 'discipline').
 * @param {string}   props.value     Selected term slug, or ''.
 * @param {Function} props.onChange  ( slug: string ) => void
 * @param {string}   [props.help]    Optional help text.
 * @param {string}   [props.allLabel] Label for the empty option.
 */
export default function TermSelect( { label, taxonomy, value, onChange, help, allLabel } ) {
	const terms = useSelect( ( select ) => select( coreStore ).getEntityRecords( 'taxonomy', taxonomy, { per_page: -1, hide_empty: false } ), [ taxonomy ] );

	const options = [ { label: allLabel || __( 'All', 'lc-mdsx2026' ), value: '' } ].concat(
		( terms || [] ).map( ( term ) => ( { label: decodeEntities( term.name ), value: term.slug } ) )
	);

	return <SelectControl label={ label } value={ value || '' } options={ options } onChange={ onChange } help={ help } />;
}
