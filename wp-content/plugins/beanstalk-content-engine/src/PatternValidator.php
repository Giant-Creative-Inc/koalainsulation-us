<?php
/**
 * Pattern manifest validation.
 *
 * @package BeanstalkContentEngine
 */

namespace GiantCreative\BeanstalkContentEngine;

use WP_Error;

/**
 * Validates the content engine's manifest contract.
 */
final class PatternValidator {

	/**
	 * Validates a manifest.
	 *
	 * @param array $manifest Manifest data.
	 * @return true|WP_Error
	 */
	public function validate( array $manifest ) {
		$required_keys = array( 'id', 'label', 'description', 'version', 'postTypes', 'template', 'fields' );
		$allowed_keys  = array_merge( array( '$schema', 'editorLayout', 'structuredData' ), $required_keys );

		if ( array_diff( array_keys( $manifest ), $allowed_keys ) || array_diff( $required_keys, array_keys( $manifest ) ) ) {
			return $this->error( __( 'A pattern manifest has missing or unsupported properties.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['id'] ) || ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $manifest['id'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid ID.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['label'] ) || '' === trim( $manifest['label'] ) || ! is_string( $manifest['description'] ) || '' === trim( $manifest['description'] ) ) {
			return $this->error( __( 'A pattern manifest must have a label and description.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['version'] ) || ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/', $manifest['version'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid version.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_array( $manifest['postTypes'] ) || empty( $manifest['postTypes'] ) || count( $manifest['postTypes'] ) > 50 ) {
			return $this->error( __( 'A pattern manifest contains an unsupported post type.', 'beanstalk-content-engine' ) );
		}

		$post_types = array_filter(
			$manifest['postTypes'],
			static fn( $post_type ) => is_string( $post_type ) && (bool) preg_match( '/^[a-z0-9_-]{1,20}$/', $post_type )
		);
		if ( count( $post_types ) !== count( $manifest['postTypes'] ) || count( array_unique( $post_types ) ) !== count( $post_types ) ) {
			return $this->error( __( 'A pattern manifest contains an unsupported or duplicate post type.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $manifest['template'] ) || ! preg_match( '/^[a-z0-9-]+\.php$/', $manifest['template'] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid template.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_array( $manifest['fields'] ) || empty( $manifest['fields'] ) ) {
			return $this->error( __( 'A pattern manifest must define content fields.', 'beanstalk-content-engine' ) );
		}

		$target_names = array();
		foreach ( $manifest['fields'] as $field_id => $field ) {
			$result = $this->validate_field( $field_id, $field, $target_names );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		if ( isset( $manifest['editorLayout'] ) ) {
			$layout = $this->normalize_editor_layout( $manifest['editorLayout'], array_keys( $manifest['fields'] ) );
			if ( is_wp_error( $layout ) ) {
				return $layout;
			}
		}
		if ( isset( $manifest['structuredData'] ) ) {
			$result = $this->validate_structured_data( $manifest['structuredData'] );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return true;
	}

	/** Validates and normalizes the optional generic editor layout. */
	public function normalize_editor_layout( $layout, array $field_ids ) {
		if ( ! is_array( $layout ) || array( 'contractVersion', 'label', 'root' ) !== array_keys( $layout ) || 1 !== $layout['contractVersion'] || ! $this->bounded_string( $layout['label'], 200 ) ) {
			return $this->layout_error();
		}
		$state = array( 'nodes' => 0, 'ids' => array(), 'fields' => array(), 'allowed_fields' => array_fill_keys( $field_ids, true ) );
		$root  = $this->normalize_layout_node( $layout['root'], 1, $state );
		return is_wp_error( $root ) ? $root : array( 'contractVersion' => 1, 'label' => trim( $layout['label'] ), 'root' => $root );
	}

	private function normalize_layout_node( $node, int $depth, array &$state ) {
		$containers = array( 'section', 'stack', 'columns', 'grid', 'group' );
		$types = array_merge( $containers, array( 'field', 'field-group', 'placeholder', 'divider', 'label' ) );
		if ( ! is_array( $node ) || $depth > 8 || ++$state['nodes'] > 200 || ! isset( $node['type'] ) || ! in_array( $node['type'], $types, true ) ) return $this->layout_error();
		$type = $node['type'];
		if ( isset( $node['writerGuidance'] ) && ! $this->bounded_string( $node['writerGuidance'], 500 ) ) return $this->layout_error();
		if ( in_array( $type, $containers, true ) ) {
			$allowed = array_merge( array( 'type', 'children', 'gap', 'align', 'writerGuidance' ), in_array( $type, array( 'section', 'group' ), true ) ? array( 'id', 'label' ) : array(), 'columns' === $type ? array( 'ratio', 'mobileOrder' ) : array(), 'grid' === $type ? array( 'columns', 'mobileOrder' ) : array() );
			if ( array_diff( array_keys( $node ), $allowed ) || ! isset( $node['children'] ) || ! is_array( $node['children'] ) || empty( $node['children'] ) || count( $node['children'] ) > 50 ) return $this->layout_error();
			$out = array( 'type' => $type );
			if ( isset( $node['id'] ) ) { if ( ! is_string( $node['id'] ) || ! preg_match( '/^[a-z][a-z0-9-]{0,79}$/', $node['id'] ) || isset( $state['ids'][ $node['id'] ] ) ) return $this->layout_error(); $state['ids'][ $node['id'] ] = true; $out['id'] = $node['id']; }
			if ( isset( $node['label'] ) ) { if ( ! $this->bounded_string( $node['label'], 200 ) ) return $this->layout_error(); $out['label'] = trim( $node['label'] ); }
			if ( isset( $node['gap'] ) ) { if ( ! in_array( $node['gap'], array( 'small', 'medium', 'large' ), true ) ) return $this->layout_error(); $out['gap'] = $node['gap']; }
			if ( isset( $node['align'] ) ) { if ( ! in_array( $node['align'], array( 'start', 'center', 'end', 'stretch' ), true ) ) return $this->layout_error(); $out['align'] = $node['align']; }
			if ( 'columns' === $type ) { if ( count( $node['children'] ) > 4 || ! isset( $node['ratio'] ) || ! is_array( $node['ratio'] ) || count( $node['ratio'] ) !== count( $node['children'] ) || array_diff( $node['ratio'], array( 'equal', 'one-third', 'two-thirds', 'one-quarter', 'three-quarters' ) ) ) return $this->layout_error(); $out['ratio'] = array_values( $node['ratio'] ); }
			if ( 'grid' === $type ) { if ( ! isset( $node['columns'] ) || ! is_int( $node['columns'] ) || $node['columns'] < 1 || $node['columns'] > 6 ) return $this->layout_error(); $out['columns'] = $node['columns']; }
			if ( isset( $node['mobileOrder'] ) ) { $order = $node['mobileOrder']; if ( ! is_array( $order ) || count( $order ) !== count( $node['children'] ) || count( array_unique( $order, SORT_REGULAR ) ) !== count( $order ) ) return $this->layout_error(); foreach ( $order as $index ) if ( ! is_int( $index ) || $index < 0 || $index >= count( $node['children'] ) ) return $this->layout_error(); $out['mobileOrder'] = array_values( $order ); }
			$out['children'] = array(); foreach ( $node['children'] as $child ) { $normalized = $this->normalize_layout_node( $child, $depth + 1, $state ); if ( is_wp_error( $normalized ) ) return $normalized; $out['children'][] = $normalized; }
			return $this->layout_guidance( $node, $out );
		}
		if ( 'field' === $type ) { if ( array_diff( array_keys( $node ), array( 'type', 'fieldId', 'display', 'writerGuidance' ) ) || ! isset( $state['allowed_fields'][ $node['fieldId'] ?? '' ] ) || isset( $state['fields'][ $node['fieldId'] ] ) || ! in_array( $node['display'] ?? '', array( 'eyebrow', 'heading', 'subheading', 'body', 'list', 'button', 'caption', 'quote', 'faq-question', 'faq-answer' ), true ) ) return $this->layout_error(); $state['fields'][ $node['fieldId'] ] = true; return $this->layout_guidance( $node, array( 'type' => $type, 'fieldId' => $node['fieldId'], 'display' => $node['display'] ) ); }
		if ( 'field-group' === $type ) { if ( array_diff( array_keys( $node ), array( 'type', 'label', 'layout', 'fieldIds', 'writerGuidance' ) ) || ! $this->bounded_string( $node['label'] ?? null, 200 ) || ! in_array( $node['layout'] ?? '', array( 'stack', 'two-column-list' ), true ) || ! is_array( $node['fieldIds'] ?? null ) || empty( $node['fieldIds'] ) || count( $node['fieldIds'] ) > 20 || count( array_unique( $node['fieldIds'] ) ) !== count( $node['fieldIds'] ) ) return $this->layout_error(); foreach ( $node['fieldIds'] as $field_id ) { if ( ! isset( $state['allowed_fields'][ $field_id ] ) || isset( $state['fields'][ $field_id ] ) ) return $this->layout_error(); $state['fields'][ $field_id ] = true; } return $this->layout_guidance( $node, array( 'type' => $type, 'label' => trim( $node['label'] ), 'layout' => $node['layout'], 'fieldIds' => array_values( $node['fieldIds'] ) ) ); }
		if ( 'placeholder' === $type ) { if ( array_diff( array_keys( $node ), array( 'type', 'role', 'label', 'ownership', 'count', 'writerGuidance' ) ) || ! in_array( $node['role'] ?? '', array( 'media', 'form', 'collection', 'testimonial', 'map', 'video', 'embed', 'navigation', 'related-content', 'decorative', 'custom-component' ), true ) || ! in_array( $node['ownership'] ?? '', array( 'content-center', 'wordpress', 'wordpress-derived' ), true ) || ! $this->bounded_string( $node['label'] ?? null, 200 ) || isset( $node['count'] ) && ( ! is_int( $node['count'] ) || $node['count'] < 1 || $node['count'] > 20 ) ) return $this->layout_error(); return $this->layout_guidance( $node, array_filter( array( 'type' => $type, 'role' => $node['role'], 'label' => trim( $node['label'] ), 'ownership' => $node['ownership'], 'count' => $node['count'] ?? null ), static fn( $value ) => null !== $value ) ); }
		if ( 'divider' === $type ) return array( 'type' => 'divider' );
		if ( array_diff( array_keys( $node ), array( 'type', 'label', 'writerGuidance' ) ) || ! $this->bounded_string( $node['label'] ?? null, 200 ) ) return $this->layout_error();
		return $this->layout_guidance( $node, array( 'type' => 'label', 'label' => trim( $node['label'] ) ) );
	}

	private function layout_guidance( array $node, array $out ): array { if ( isset( $node['writerGuidance'] ) ) $out['writerGuidance'] = trim( $node['writerGuidance'] ); return $out; }
	private function bounded_string( $value, int $max ): bool { return is_string( $value ) && '' !== trim( $value ) && strlen( $value ) <= $max; }
	private function layout_error(): WP_Error { return new WP_Error( 'beanstalk_invalid_editor_layout', __( 'A pattern manifest has an invalid editor layout.', 'beanstalk-content-engine' ) ); }

	/** Validates the optional template-owned structured-data contract. */
	private function validate_structured_data( $contract ) {
		$keys = array( 'contractVersion', 'profile', 'label', 'produces', 'fields' );
		if ( ! is_array( $contract ) || array_diff( array_keys( $contract ), $keys ) || array_diff( $keys, array_keys( $contract ) )
			|| 1 !== $contract['contractVersion'] || ! is_string( $contract['profile'] ) || ! preg_match( '/^[a-z0-9-]+\/[a-z0-9-]+$/', $contract['profile'] )
			|| ! $this->bounded_string( $contract['label'], 200 ) || ! is_array( $contract['produces'] ) || empty( $contract['produces'] )
			|| ! is_array( $contract['fields'] ) || empty( $contract['fields'] ) ) return $this->error( __( 'A pattern manifest has an invalid structured-data contract.', 'beanstalk-content-engine' ) );
		$entities = array( 'WebPage', 'Service', 'HomeAndConstructionBusiness', 'BreadcrumbList', 'FAQPage' );
		if ( count( $contract['produces'] ) > 10 || count( array_unique( $contract['produces'] ) ) !== count( $contract['produces'] ) || array_diff( $contract['produces'], $entities ) ) return $this->error( __( 'A pattern manifest has unsupported or duplicate structured-data entities.', 'beanstalk-content-engine' ) );
		foreach ( $contract['fields'] as $id => $field ) {
			$field_keys = array( 'label', 'type', 'required', 'source' );
			if ( ! is_string( $id ) || ! preg_match( '/^[a-z][a-z0-9_]*$/', $id ) || ! is_array( $field ) || array_diff( array_keys( $field ), $field_keys ) || array_diff( $field_keys, array_keys( $field ) )
				|| ! $this->bounded_string( $field['label'], 200 ) || ! in_array( $field['type'], array( 'text', 'url', 'integer', 'string-list', 'boolean' ), true )
				|| ! is_bool( $field['required'] ) || ! in_array( $field['source'], array( 'content-center', 'wordpress', 'wordpress-derived' ), true ) ) return $this->error( __( 'A pattern manifest structured-data field is invalid.', 'beanstalk-content-engine' ) );
		}
		return true;
	}

	/**
	 * Validates one field.
	 *
	 * @param mixed $field_id    Field ID.
	 * @param mixed $field       Field data.
	 * @param array $target_names Seen target names.
	 * @return true|WP_Error
	 */
	private function validate_field( $field_id, $field, array &$target_names ) {
		$allowed_keys = array( 'label', 'type', 'required', 'target' );

		if ( ! is_string( $field_id ) || ! preg_match( '/^[a-z][a-z0-9_]*$/', $field_id ) || ! is_array( $field ) ) {
			return $this->error( __( 'A pattern manifest contains an invalid field.', 'beanstalk-content-engine' ) );
		}

		if ( array_diff( array_keys( $field ), $allowed_keys ) || array_diff( $allowed_keys, array_keys( $field ) ) ) {
			return $this->error( __( 'A pattern manifest field has invalid properties.', 'beanstalk-content-engine' ) );
		}

		if ( ! is_string( $field['label'] ) || '' === trim( $field['label'] ) || ! in_array( $field['type'], array( 'text', 'rich-text', 'image' ), true ) || ! is_bool( $field['required'] ) || ! is_array( $field['target'] ) ) {
			return $this->error( __( 'A pattern manifest field has an invalid definition.', 'beanstalk-content-engine' ) );
		}

		$target       = $field['target'];
		$valid_target = 'image' === $field['type']
			? 'core/image' === ( $target['block'] ?? '' ) && 'image' === ( $target['attribute'] ?? '' )
			: ( in_array( $target['block'] ?? '', array( 'core/heading', 'core/paragraph' ), true ) && 'content' === ( $target['attribute'] ?? '' ) )
				|| ( 'text' === $field['type'] && 'core/details' === ( $target['block'] ?? '' ) && 'summary' === ( $target['attribute'] ?? '' ) );
		if ( array( 'block', 'name', 'attribute' ) !== array_keys( $target )
			|| ! $valid_target
			|| ! preg_match( '/^[a-z0-9-]+-field-[a-z0-9-]+$/', $target['name'] )
			|| isset( $target_names[ $target['name'] ] ) ) {
			return $this->error( __( 'A pattern manifest has an invalid or duplicate field target.', 'beanstalk-content-engine' ) );
		}

		$target_names[ $target['name'] ] = true;
		return true;
	}

	/**
	 * Creates a consistent validation error.
	 *
	 * @param string $message Error message.
	 * @return WP_Error
	 */
	private function error( string $message ): WP_Error {
		return new WP_Error( 'beanstalk_invalid_manifest', $message );
	}
}
