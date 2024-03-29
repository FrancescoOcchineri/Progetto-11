<?php

namespace wpforo\classes;

use stdClass;

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

class Activity {
	private $default;
	public  $activity;
	private $actions;
	public  $notifications = [];

	public function __construct() {
		add_action( 'wpforo_after_init', [ $this, 'init' ] );
	}

	public function init() {
		$this->init_defaults();
		$this->activity = $this->default->activity;
		$this->init_hooks();
		$this->init_actions();
		if( is_user_logged_in() && wpforo_setting( 'notifications', 'notifications' ) ) {
			$this->notifications = $this->get_notifications();
		}
	}

	private function init_actions() {
		$this->actions = [
			'edit_topic'    => [
				'title'       => wpforo_phrase( 'Edit Topic', false ),
				'icon'        => '',
				'description' => wpforo_phrase( 'This topic was modified %s by %s', false ),
				'before'      => '<div class="wpf-post-edited"><i class="far fa-edit"></i>',
				'after'       => '</div>',
			],
			'edit_post'     => [
				'title'       => wpforo_phrase( 'Edit Post', false ),
				'icon'        => '',
				'description' => wpforo_phrase( 'This post was modified %s by %s', false ),
				'before'      => '<div class="wpf-post-edited"><i class="far fa-edit"></i>',
				'after'       => '</div>',
			],
			'new_reply'     => [
				'title'       => wpforo_phrase( 'New Reply', false ),
				'icon'        => '<i class="fas fa-reply fa-rotate-180"></i>',
				'description' => wpforo_phrase( 'New reply from %1$s, %2$s', false ),
				'before'      => '<li class="wpf-new_reply">',
				'after'       => '</li>',
			],
			'new_like'      => [
				'title'       => wpforo_phrase( 'New Like', false ),
				'icon'        => '<i class="fas fa-heart"></i>',
				'description' => wpforo_phrase( 'New like from %1$s, %2$s', false ),
				'before'      => '<li class="wpf-new_like">',
				'after'       => '</li>',
			],
            'new_dislike'      => [
                'title'       => wpforo_phrase( 'New Dislike', false ),
                'icon'        => '<i class="fas fa-heart-crack"></i>',
                'description' => wpforo_phrase( 'New dislike from %1$s, %2$s', false ),
                'before'      => '<li class="wpf-new_dislike">',
                'after'       => '</li>',
            ],
			'new_up_vote'   => [
				'title'       => wpforo_phrase( 'New Up Vote', false ),
				'icon'        => '<i class="fas fa-arrow-alt-circle-up"></i>',
				'description' => wpforo_phrase( 'New up vote from %1$s, %2$s', false ),
				'before'      => '<li class="wpf-new_up_vote">',
				'after'       => '</li>',
			],
			'new_down_vote' => [
				'title'       => wpforo_phrase( 'New Down Vote', false ),
				'icon'        => '<i class="fas fa-arrow-alt-circle-down"></i>',
				'description' => wpforo_phrase( 'New down vote from %1$s, %2$s', false ),
				'before'      => '<li class="wpf-new_down_vote">',
				'after'       => '</li>',
			],
			'new_mention'   => [
				'title'       => wpforo_phrase( 'New User Mentioning', false ),
				'icon'        => '<i class="fas fa-at"></i>',
				'description' => wpforo_phrase( '%1$s has mentioned you, %2$s', false ),
				'before'      => '<li class="wpf-new_mention">',
				'after'       => '</li>',
			],
			'default'       => [
				'title'       => wpforo_phrase( 'New Notification', false ),
				'icon'        => '<i class="fas fa-bell"></i>',
				'description' => wpforo_phrase( 'New notification from %1$s, %2$s', false ),
				'before'      => '<li class="wpf-new_note">',
				'after'       => '</li>',
			],
		];

		$this->actions = apply_filters( 'wpforo_register_actions', $this->actions );
	}

	private function init_defaults() {
		$this->default                  = new stdClass();
		$this->default->activity        = [
			'id'            => 0,
			'type'          => '',
			'itemid'        => 0,
			'itemtype'      => '',
			'itemid_second' => 0,
			'userid'        => 0,
			'name'          => '',
			'email'         => '',
			'date'          => 0,
			'content'       => '',
			'permalink'     => '',
			'new'           => 0,
		];
		$this->default->activity_format = [
			'id'            => '%d',
			'type'          => '%s',
			'itemid'        => '%d',
			'itemtype'      => '%s',
			'itemid_second' => '%d',
			'userid'        => '%d',
			'name'          => '%s',
			'email'         => '%s',
			'date'          => '%d',
			'content'       => '%s',
			'permalink'     => '%s',
			'new'           => '%d',
		];
		$this->default->sql_select_args = [
			'type'              => null,
			'userid'            => null,
			'itemtype'          => null,
			'new'               => null,
			'include'           => [],
			'exclude'           => [],
			'userids_include'   => [],
			'userids_exclude'   => [],
			'types_include'     => [],
			'types_exclude'     => [],
			'itemids_include'   => [],
			'itemids_exclude'   => [],
			'itemtypes_include' => [],
			'itemtypes_exclude' => [],
			'emails_include'    => [],
			'emails_exclude'    => [],
			'orderby'           => 'id',
			'order'             => 'ASC',
			'offset'            => null,
			'row_count'         => null,
		];
		
		$this->default = apply_filters( 'wpforo_activity_after_init_defaults', $this->default );
	}

	private function init_hooks() {
		if( wpforo_setting( 'posting', 'edit_topic' ) ) {
			add_action( 'wpforo_after_edit_topic', [ $this, 'after_edit_topic' ] );
		}
		if( wpforo_setting( 'posting', 'edit_post' ) ) {
			add_action( 'wpforo_after_edit_post', [ $this, 'after_edit_post' ] );
		}

		if( WPF()->current_userid && wpforo_setting( 'notifications', 'notifications' ) ) {
			if( wpforo_setting( 'notifications', 'notifications_bar' ) ) {
				add_action( 'wpforo_before_search_toggle', [ $this, 'bell' ] );
			}
			add_action( 'wpforo_after_add_post', [ $this, 'after_add_post' ], 10, 2 );
			add_action( 'wpforo_post_status_update', [ $this, 'update_notification' ], 10, 2 );
			add_action( 'wpforo_like', [ $this, 'after_like' ] );
			add_action( 'wpforo_dislike', [ $this, 'after_dislike' ] );
            add_action( 'wpforo_undo_like', [ $this, 'after_undo_like' ] );
            add_action( 'wpforo_undo_dislike', [ $this, 'after_undo_dislike' ] );
			add_action( 'wpforo_vote', [ $this, 'after_vote' ], 10, 2 );
		}
	}

	private function filter_built_html_rows( $rows ) {
		$_rows = [];
		foreach( $rows as $row_key => $row ) {
			$in_array = false;
			if( $_rows ) {
				foreach( $_rows as $_row_key => $_row ) {
					if( in_array( $row, $_row ) ) {
						$in_array  = true;
						$match_key = $_row_key;
						break;
					}
				}
			}
			if( $in_array && isset( $match_key ) ) {
				$_rows[ $match_key ]['times'] ++;
			} else {
				$_rows[ $row_key ]['html']  = $row;
				$_rows[ $row_key ]['times'] = 1;
			}
		}

		$rows = [];
		foreach( $_rows as $_row ) {
			$times = '';
			if( $_row['times'] > 1 ) {
				$times = ' ' . sprintf(
						wpforo_phrase( '%d times', false ),
						$_row['times']
					);
			}

			$rows[] = sprintf( $_row['html'], $times );
		}

		$limit = wpforo_setting( 'posting', 'edit_log_display_limit' );
		if( $limit ) $rows = array_slice( $rows, ( - 1 * $limit ), $limit );

		return $rows;
	}

	private function parse_activity( $data ) {
		return apply_filters( 'wpforo_activty_parse_activity', array_merge( $this->default->activity, $data ) );
	}

	private function parse_args( $args ) {
		$args = wpforo_parse_args( $args, $this->default->sql_select_args );

		$args['include'] = wpforo_parse_args( $args['include'] );
		$args['exclude'] = wpforo_parse_args( $args['exclude'] );

		$args['userids_include'] = wpforo_parse_args( $args['userids_include'] );
		$args['userids_exclude'] = wpforo_parse_args( $args['userids_exclude'] );

		$args['types_include'] = wpforo_parse_args( $args['types_include'] );
		$args['types_exclude'] = wpforo_parse_args( $args['types_exclude'] );

		$args['itemids_include'] = wpforo_parse_args( $args['itemids_include'] );
		$args['itemids_exclude'] = wpforo_parse_args( $args['itemids_exclude'] );

		$args['itemtypes_include'] = wpforo_parse_args( $args['itemtypes_include'] );
		$args['itemtypes_exclude'] = wpforo_parse_args( $args['itemtypes_exclude'] );

		$args['emails_include'] = wpforo_parse_args( $args['emails_include'] );
		$args['emails_exclude'] = wpforo_parse_args( $args['emails_exclude'] );

		return $args;
	}

	private function build_sql_select( $args ) {
		$args = $this->parse_args( $args );

		$wheres = [];

		if( ! is_null( $args['type'] ) ) $wheres[] = "`type` = '" . esc_sql( $args['type'] ) . "'";
		if( ! is_null( $args['itemtype'] ) ) $wheres[] = "`itemtype` = '" . esc_sql( $args['itemtype'] ) . "'";
		if( ! is_null( $args['userid'] ) ) $wheres[] = "`userid` = " . intval( $args['userid'] );
		if( ! is_null( $args['new'] ) ) $wheres[] = "`new` = " . intval( $args['new'] );

		if( ! empty( $args['include'] ) ) $wheres[] = "`id` IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['include'] ) ) . ")";
		if( ! empty( $args['exclude'] ) ) $wheres[] = "`id` NOT IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['exclude'] ) ) . ")";

		if( ! empty( $args['userids_include'] ) ) $wheres[] = "`userid` IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['userids_include'] ) ) . ")";
		if( ! empty( $args['userids_exclude'] ) ) $wheres[] = "`userid` NOT IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['userids_exclude'] ) ) . ")";

		if( ! empty( $args['types_include'] ) ) $wheres[] = "`type` IN('" . implode( "','", array_map( 'trim', $args['types_include'] ) ) . "')";
		if( ! empty( $args['types_exclude'] ) ) $wheres[] = "`type` NOT IN('" . implode( "','", array_map( 'trim', $args['types_exclude'] ) ) . "')";

		if( ! empty( $args['itemids_include'] ) ) $wheres[] = "`itemid` IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['itemids_include'] ) ) . ")";
		if( ! empty( $args['itemids_exclude'] ) ) $wheres[] = "`itemid` NOT IN(" . implode( ',', array_map( 'wpforo_bigintval', $args['itemids_exclude'] ) ) . ")";

		if( ! empty( $args['itemtypes_include'] ) ) $wheres[] = "`itemtype` IN('" . implode( "','", array_map( 'trim', $args['itemtypes_include'] ) ) . "')";
		if( ! empty( $args['itemtypes_exclude'] ) ) $wheres[] = "`itemtype` NOT IN('" . implode( "','", array_map( 'trim', $args['itemtypes_exclude'] ) ) . "')";

		if( ! empty( $args['emails_include'] ) ) $wheres[] = "`email` IN('" . implode( "','", array_map( 'trim', $args['emails_include'] ) ) . "')";
		if( ! empty( $args['emails_exclude'] ) ) $wheres[] = "`email` NOT IN('" . implode( "','", array_map( 'trim', $args['emails_exclude'] ) ) . "')";

		$sql = "SELECT * FROM " . WPF()->tables->activity;
		if( $wheres ) $sql .= " WHERE " . implode( " AND ", $wheres );
		$sql .= " ORDER BY " . $args['orderby'] . " " . $args['order'];
		if( $args['row_count'] ) {
			if( ! empty( $args['offset'] ) ) {
				$sql .= " LIMIT " . wpforo_bigintval( $args['offset'] ) . "," . wpforo_bigintval( $args['row_count'] );
			} else {
				$sql .= " LIMIT " . wpforo_bigintval( $args['row_count'] );
			}
		}

		return $sql;
	}

	public function get_activity( $args ) {
		if( ! $args ) return false;

		return $this->parse_activity( (array) WPF()->db->get_row( $this->build_sql_select( $args ), ARRAY_A ) );
	}

	public function get_activities( $args ) {
		if( ! $args ) return [];

		return array_map( [ $this, 'parse_activity' ], (array) WPF()->db->get_results( $this->build_sql_select( $args ), ARRAY_A ) );
	}

	public function after_edit_topic( $topic ) {
		$data = [
			'type'      => 'edit_topic',
			'itemid'    => $topic['topicid'],
			'itemtype'  => 'topic',
			'userid'    => WPF()->current_userid,
			'name'      => WPF()->current_user_display_name,
			'email'     => WPF()->current_user_email,
			'permalink' => wpforo_topic( $topic['topicid'], 'url' ),
		];

		$this->add( $data );
	}

	public function after_edit_post( $post ) {
		$data = [
			'type'      => 'edit_post',
			'itemid'    => $post['postid'],
			'itemtype'  => 'post',
			'userid'    => WPF()->current_userid,
			'name'      => WPF()->current_user_display_name,
			'email'     => WPF()->current_user_email,
			'permalink' => wpforo_post( $post['postid'], 'url' ),
		];

		$this->add( $data );
	}

	public function after_add_post( $post, $topic ) {
		$this->add_notification_new_reply( 'new_reply', $post, $topic );
	}

	private function add( $data ) {
		if( empty( $data ) ) return false;
		$activity = array_merge( $this->default->activity, $data );
		unset( $activity['id'] );

		if( ! $activity['type'] || ! $activity['itemid'] || ! $activity['itemtype'] ) return false;
		if( ! $activity['date'] ) $activity['date'] = time();

		$activity = apply_filters( 'wpforo_add_activity_data_filter', $activity );
		do_action( 'wpforo_before_add_activity', $activity );
		
		$activity = wpforo_array_ordered_intersect_key( $activity, $this->default->activity_format );
		if( WPF()->db->insert(
			WPF()->tables->activity,
			$activity,
			wpforo_array_ordered_intersect_key( $this->default->activity_format, $activity )
		) ) {
			$activity['id'] = WPF()->db->insert_id;
			do_action( 'wpforo_after_add_activity', $activity );
			
			return $activity['id'];
		}

		return false;
	}

	private function edit( $data, $where ) {
		if( empty( $data ) || empty( $where ) ) return false;
		if( is_numeric( $where ) ) $where = [ 'id' => $where ];
		$data  = (array) $data;
		$where = (array) $where;
		
		$data  = apply_filters( 'wpforo_activity_edit_data_filter',  $data );
		$where = apply_filters( 'wpforo_activity_edit_where_filter', $where );
		do_action( 'wpforo_before_edit_activity', $data, $where );

		$data  = wpforo_array_ordered_intersect_key( $data, $this->default->activity_format );
		$where = wpforo_array_ordered_intersect_key( $where, $this->default->activity_format );
		if( false !== WPF()->db->update(
				WPF()->tables->activity,
				$data,
				$where,
				wpforo_array_ordered_intersect_key( $this->default->activity_format, $data ),
				wpforo_array_ordered_intersect_key( $this->default->activity_format, $where )
			) ) {
			do_action( 'wpforo_after_edit_activity', $data, $where );
			
			return true;
		}

		return false;
	}

	private function delete( $where ) {
		if( empty( $where ) ) return false;
		if( is_numeric( $where ) ) $where = [ 'id' => $where ];
		$where = (array) $where;
		
		$where = apply_filters( 'wpforo_activity_delete_where_filter', $where );
		do_action( 'wpforo_before_delete_activity', $where );
		
		$where = wpforo_array_ordered_intersect_key( $where, $this->default->activity_format );
		if( false !== WPF()->db->delete(
				WPF()->tables->activity,
				$where,
				wpforo_array_ordered_intersect_key( $this->default->activity_format, $where )
			) ) {
			do_action( 'wpforo_after_delete_activity', $where );
			
			return true;
		}

		return false;
	}

	public function build( $itemtype, $itemid, $type, $echo = false ) {
		$rows = [];
		$args = [
			'itemtypes_include' => $itemtype,
			'itemids_include'   => $itemid,
			'types_include'     => $type,
		];
		if( $activities = $this->get_activities( $args ) ) {
			foreach( $activities as $activity ) {
				switch( $activity['type'] ) {
					case 'edit_topic':
					case 'edit_post':
						$rows[] = $this->_build_edit_topic_edit_post( $activity );
					break;
				}
			}
		}

		$rows = $this->filter_built_html_rows( $rows );

		$html = ( $rows ? implode( '', $rows ) : '' );
		if( $echo ) echo $html;

		return $html;
	}

	private function _build_edit_topic_edit_post( $activity ) {
		$html   = '';
		$type   = $activity['type'];
		$userid = $activity['userid'];
		$date   = wpforo_date( $activity['date'], 'ago', false ) . '%s';

		if( $userid ) {
			$profile_url  = wpforo_member( $userid, 'profile_url' );
			$display_name = wpforo_member( $userid, 'display_name' );
			$user         = sprintf( '<a href="%s">%s</a>', $profile_url, $display_name );
		} else {
			$user = $activity['name'] ?: wpforo_phrase( 'Guest', false );
		}

		if( wpfval( $this->actions, $type, 'before' ) ) {
			$html = $this->actions[ $type ]['before'];
			$html = apply_filters( 'wpforo_activity_action_html_before', $html, $activity );
		}
		if( wpfval( $this->actions, $type, 'description' ) ) {
			$html .= sprintf( $this->actions[ $activity['type'] ]['description'], $date, str_replace( '%', '%%', $user ) );
			$html = apply_filters( 'wpforo_activity_action_html', $html, $activity );
		}
		if( wpfval( $this->actions, $type, 'after' ) ) {
			$html .= $this->actions[ $type ]['after'];
			$html = apply_filters( 'wpforo_activity_action_html_after', $html, $activity );
		}

		return $html;
	}

	public function bell( $class = 'wpf-alerts' ) {
		wp_enqueue_script( 'wpforo-widgets-js' );

		$class   = ( ! $class ) ? 'wpf-alerts' : $class;
		$count   = ( ! empty( $this->notifications ) ) ? count( $this->notifications ) : 0;
		$phrase  = ( $count > 1 ) ? wpforo_phrase( 'You have new notifications', false ) : wpforo_phrase( 'You have a new notification', false );
		$tooltip = ' wpf-tooltip="' . esc_attr( $phrase ) . '" wpf-tooltip-size="middle"';
		?>
        <div class="<?php echo esc_attr( $class ) ?> <?php echo ( $count ) ? 'wpf-new' : ''; ?>">
			<?php if( $count ): ?>
                <div class="wpf-bell" <?php echo $tooltip ?>>
                    <i class="fas fa-bell"></i>
                    <span class="wpf-alerts-count"><?php echo $count ?></span>
                </div>
			<?php else: ?>
                <div class="wpf-bell">
                    <i class="far fa-bell"></i>
                </div>
			<?php endif; ?>
        </div>
		<?php
	}

	public function notifications() {
		?>
        <div class="wpf-notifications">
            <div class="wpf-notification-head">
                <i class="far fa-bell"></i> <?php wpforo_phrase( 'Notifications' ) ?>
            </div>
            <div class="wpf-notification-content">
                <div class="wpf-nspin"><i class="fas fa-spinner fa-spin"></i></div>
            </div>
            <div class="wpf-notification-actions">
                <span class="wpf-action wpf-notification-action-clear-all" data-foro_n="<?php echo wp_create_nonce( 'wpforo_clear_notifications' ) ?>"><?php wpforo_phrase( 'Clear all' ) ?></span>
            </div>
        </div>
		<?php
	}

	public function notifications_list( $echo = true ) {
		$items     = [];
		$list_html = '';
		if( ! empty( $this->notifications ) && is_array( $this->notifications ) ) {
			$list_html .= '<ul>';
			foreach( $this->notifications as $n ) {
				if( $type = wpfval( $n, 'type' ) ) {
					$html              = wpfval( $this->actions, $type ) ? $this->actions[ $type ] : $this->actions['default'];
					$items[ $n['id'] ] = $html['before'];
					if( wpfval( $n, 'itemid_second' ) ) {
						$member      = wpforo_member( $n['itemid_second'] );
						$member_name = wpfval( $member, 'display_name' ) ? $member['display_name'] : wpforo_phrase( 'Guest', false );
					} else {
						$member_name = wpfval( $n, 'name' ) ? $n['name'] : wpforo_phrase( 'Guest', false );
					}
					if( strpos( (string) $n['permalink'], '#' ) === false ) {
						$n['permalink'] = wp_nonce_url( $n['permalink'] . '?_nread=' . $n['id'], 'wpforo_mark_notification_read', 'foro_n' );
					} else {
						$n['permalink'] = str_replace( '#', '?_nread=' . $n['id'] . '#', $n['permalink'] );
						$n['permalink'] = wp_nonce_url( $n['permalink'], 'wpforo_mark_notification_read', 'foro_n' );
					}
					$date              = wpforo_date( $n['date'], 'ago', false );
					$length            = apply_filters( 'wpforo_notification_description_length', 40 );
					$items[ $n['id'] ] .= '<div class="wpf-nleft">' . $html['icon'] . '</div>';
					$items[ $n['id'] ] .= '<div class="wpf-nright">';
					$items[ $n['id'] ] .= '<a href="' . esc_url_raw( $n['permalink'] ) . '">';
					$items[ $n['id'] ] .= sprintf( $html['description'], '<strong>' . $member_name . '</strong>', $date );
					$items[ $n['id'] ] .= '</a>';
					$items[ $n['id'] ] .= '<div class="wpf-ndesc">' . stripslashes( wpforo_text( (string) $n['content'], $length, false ) ) . '</div>';
					$items[ $n['id'] ] .= '</div>';
					$items[ $n['id'] ] .= $html['after'];
				}
			}
			$items     = apply_filters( 'wpforo_notifications_list', $items );
			$list_html .= implode( "\r\n", $items );
			$list_html .= '</ul>';
		} else {
			$list_html = $this->get_no_notifications_html();
		}
		if( $echo ) echo $list_html;

		return $list_html;
	}

	public function get_no_notifications_html() {
		return '<div class="wpf-no-notification">' . wpforo_phrase( 'You have no new notifications', false ) . '</div>';
	}

	public function get_notifications() {
		$args = [ 'itemtype' => 'alert', 'userid' => WPF()->current_userid, 'row_count' => 100, 'orderby' => 'date', 'order' => 'DESC' ];
		$args = apply_filters( 'wpforo_get_notifications_args', $args );

		return $this->get_activities( $args );
	}

	public function add_notification_new_reply( $type, $post, $topic = [] ): void {
		if( ! wpfval( $post, 'status' ) ) {
			$notification = [
				'type'          => $type,
				'itemid'        => $post['postid'],
				'itemtype'      => 'alert',
				'itemid_second' => $post['userid'],
				'name'          => $post['name'],
				'email'         => $post['email'],
				'content'       => $post['title'],
				'permalink'     => $post['posturl'],
				'new'           => 1,
			];
			// Notify replied person
			$replied_post = wpforo_post( $post['parentid'] );
			if( ! empty( $replied_post ) && wpfval( $replied_post, 'userid' ) != wpfval( $post, 'userid' ) ) {
				$notification['userid'] = $replied_post['userid'];
				$notification = apply_filters( 'wpforo_add_notification_new_reply_data', $notification, $type, $post, $topic, $replied_post );
				$this->add( $notification );
			}
			// Notify the topic author
			if( ! empty( $topic ) && $topic['userid'] != $post['userid'] && ! ( ! empty( $replied_post ) && $topic['userid'] == $replied_post['userid'] ) ) {
				$notification['userid'] = $topic['userid'];
				$notification = apply_filters( 'wpforo_add_notification_new_reply_data', $notification, $type, $post, $topic, $replied_post );
				$this->add( $notification );
			}
		}
	}

	public function add_notification( $type, $args ): void {
		if( $args['userid'] != WPF()->current_userid ) {
			$length       = apply_filters( 'wpforo_notification_saved_description_length', 50 );
			$notification = [
				'type'          => $type,
				'itemid'        => $args['itemid'],
				'itemtype'      => 'alert',
				'itemid_second' => WPF()->current_userid,
				'userid'        => $args['userid'],
				'name'          => WPF()->current_user_display_name,
				'email'         => WPF()->current_user_email,
				'content'       => wpforo_text( $args['content'], $length, false ),
				'permalink'     => ( wpfval( $args, 'permalink' ) ? $args['permalink'] : '#' ),
				'new'           => 1,
			];
			$notification = apply_filters( 'wpforo_add_notification_data', $notification, $type, $args );
			$this->add( $notification );
		}
	}

	public function after_like( $post ): void {
		if( $post ) {
			$args = [
				'itemid'    => $post['postid'],
				'userid'    => $post['userid'],
				'content'   => $post['body'],
				'permalink' => WPF()->post->get_url( $post['postid'] ),
			];
			$this->add_notification( 'new_like', $args );
		}
	}

    public function after_dislike( $post ): void {
        if( $post ) {
            $args = [
                'itemid'    => $post['postid'],
                'userid'    => $post['userid'],
                'content'   => $post['body'],
                'permalink' => WPF()->post->get_url( $post['postid'] ),
            ];
            $this->add_notification( 'new_dislike', $args );
        }
    }

    public function after_undo_like( $post ) {
        $args = [
            'type'          => 'new_like',
            'itemid'        => $post['postid'],
            'itemtype'      => 'alert',
            'itemid_second' => WPF()->current_userid,
        ];
        $this->delete_notification( $args );
    }

    public function after_undo_dislike( $post ) {
        $args = [
            'type'          => 'new_dislike',
            'itemid'        => $post['postid'],
            'itemtype'      => 'alert',
            'itemid_second' => WPF()->current_userid,
        ];
        $this->delete_notification( $args );
    }

	public function after_vote( $reaction, $post ) {
		if( $post ) {
			if( $reaction == 1 ) {
				$args = [
					'itemid'    => $post['postid'],
					'userid'    => $post['userid'],
					'content'   => $post['body'],
					'permalink' => WPF()->post->get_url( $post['postid'] ),
				];
				$this->add_notification( 'new_up_vote', $args );
				$args = [
					'type'          => 'new_down_vote',
					'itemid'        => $post['postid'],
					'itemtype'      => 'alert',
					'itemid_second' => WPF()->current_userid,
				];
				$this->delete_notification( $args );
			} elseif( $reaction == - 1 ) {
				$args = [
					'itemid'    => $post['postid'],
					'userid'    => $post['userid'],
					'content'   => $post['body'],
					'permalink' => WPF()->post->get_url( $post['postid'] ),
				];
				$this->add_notification( 'new_down_vote', $args );
				$args = [
					'type'          => 'new_up_vote',
					'itemid'        => $post['postid'],
					'itemtype'      => 'alert',
					'itemid_second' => WPF()->current_userid,
				];
				$this->delete_notification( $args );
			}
		}
	}

	public function delete_notification( $args ) {
		$this->delete( $args );
	}

	public function update_notification( $post, $status ) {
		$post['status']  = $status = intval($status);
		$post['posturl'] = WPF()->post->get_url( $post['postid'] );
		if( wpfval( $post, 'topicid' ) ) {
			$topic = WPF()->topic->get_topic( $post['topicid'] );
			if( $status ) {
				$args = [
					'type'     => 'new_reply',
					'itemid'   => $post['postid'],
					'itemtype' => 'alert',
				];
				$this->delete_notification( $args );
			} else {
				$this->add_notification_new_reply( 'new_reply', $post, $topic );
			}
		}
	}

	public function read_notification( $id, $userid = null ) {
		$userid = is_null( $userid ) ? WPF()->current_userid : $userid;
		$args   = [
			'id'     => $id,
			'userid' => $userid,
		];
		$this->delete_notification( $args );
	}

	public function clear_notifications( $userid = null ) {
		$userid = is_null( $userid ) ? WPF()->current_userid : $userid;
		$args   = [
			'userid' => $userid,
		];
		$this->delete_notification( $args );
	}

}
