<?php

class MJA_Reactions {
	/*
		protected function __construct() {
			$this->setup_hooks();
		}
		protected function setup_hooks(){
		}
	*/
	public function has_like( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		$total_liked = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS total_liked FROM {$wpdb->prefix}mjaads_like WHERE post_id = %s", $id), ARRAY_A );
		$total_liked = isset( $total_liked[0]['total_liked'] ) ? $total_liked[0]['total_liked'] : (
			$total_liked['total_liked'] ? $total_liked['total_liked'] : false
		);
		return ( $total_liked && $total_liked >= 1 );
	}
	public function get_liked( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		$total_liked = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS total_liked FROM {$wpdb->prefix}mjaads_like WHERE post_id = %s", $id), ARRAY_A );
		$total_liked = isset( $total_liked[0]['total_liked'] ) ? $total_liked[0]['total_liked'] : (
			$total_liked['total_liked'] ? $total_liked['total_liked'] : ''
		);
		$prefix = $this->is_liked( $id );
		if( $total_liked >= 1 ){
			$people = $wpdb->get_results( $wpdb->prepare( "SELECT u.display_name FROM {$wpdb->prefix}mjaads_like l JOIN {$wpdb->prefix}users u WHERE l.post_id = %s AND u.ID = l.visitor_id AND l.visitor_id != %s ORDER BY l.ID DESC LIMIT 0, 1;", $id, wp_get_current_user()->ID ) )[0];
			// $people = ( count($people) > 0 && isset( $people['display_name'] ) && !empty( $people['display_name'] ) ) ? $people['display_name'] : false;
		}
		switch( $total_liked ) {
			case '0' :
				return 0;
				break;
			default :
				if( $prefix || $people->display_name ) {
					$total_liked = ( $prefix && $people->display_name ) ? ( $total_liked -2 ) : ( $total_liked - 1 );
					$return = ( $prefix ) ? _x( 'You', 'You', "buddypress-advertising" ) . ( ( $total_liked >= 1 ) ? ', ' : '' ) : '';
					$return .= ( $people->display_name ) ? $people->display_name . ( ( $total_liked >= 1 ) ? ', ' : '' ) : '';
					$return .= ( $total_liked <= 0 ) ? '' : __( 'and', "buddypress-advertising" ) . ' ';
				}
				$return .= ( $total_liked <= 0 ) ? '' : $total_liked . ' ' . __(
					sprintf(
						'other%s',
						( $total_liked == 1 ) ? '' : 's'
					),
				"buddypress-advertising" );
				$return .= ' ' . __( 'liked this ad.', "buddypress-advertising" );
				return $return;
				break;
		};
		return 0;
	}
	public function is_liked( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		$is_liked = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS is_liked FROM {$wpdb->prefix}mjaads_like WHERE post_id = %s AND visitor_id = %s LIMIT 1;", $id, wp_get_current_user()->ID ) );
		return isset( $is_liked[0]->is_liked ) && ( $is_liked[0]->is_liked > 0 ) ? true : (
			isset( $is_liked->is_liked ) && ( $is_liked->is_liked > 0 ) ? true : false
		);
	}
	public function toggle_like( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		if( $this->is_liked( $id ) ){
			return $this->remove_like( $id );
		}else{
			return $this->insert_like( $id );
		}
	}
	protected function insert_like( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}mjaads_like ( post_id, visitor_id, expression ) VALUES ( %s, %s, 0 );", $id, wp_get_current_user()->ID ) );
	}
	protected function remove_like( $id = false ) {
		// if( ! $id ) {return false;}
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mjaads_like WHERE post_id = %s AND visitor_id = %s;", $id, wp_get_current_user()->ID ) );
		return true;
	}
};

