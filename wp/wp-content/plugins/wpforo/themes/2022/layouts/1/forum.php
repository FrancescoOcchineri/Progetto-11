<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

/**
*
* @layout: Extended
* @url: http://gvectors.com/
* @version: 1.0.0
* @author: gVectors Team
* @description: Extended layout displays one level deeper information in advance.
*
*/

$cover_styles = wpforo_get_forum_cover_styles( $cat );
?>

<div class="wpfl-1 wpforo-section">
	<div class="wpforo-category" <?php echo $cover_styles['cover'] ?>>
        <div class="wpforo-cat-panel" <?php echo $cover_styles['blur'] ?>>
            <div class="cat-title" title="<?php echo esc_attr($cat['description']); ?>">
                <span class="cat-name" <?php echo $cover_styles['title'] ?>><?php echo esc_html($cat['title']); ?></span>
            </div>
            <div class="cat-stat-posts" <?php echo $cover_styles['info'] ?>><?php wpforo_phrase('Posts'); ?></div>
            <div class="cat-stat-topics" <?php echo $cover_styles['info'] ?>><?php wpforo_phrase('Topics'); ?></div>
            <br class="wpf-clear" />
        </div>
	</div><!-- wpforo-category -->
	<?php
    $forum_list = false;
    foreach($forums as $key => $forum) :
		if( !WPF()->perm->forum_can( 'vf', $forum['forumid'] ) ) continue;

        $forum_list = true;

		$sub_forums = WPF()->forum->get_forums( array( "parentid" => $forum['forumid'], "type" => 'forum' ) );
		$has_sub_forums = is_array($sub_forums) && !empty($sub_forums);

		$data = wpforo_forum($forum['forumid'], 'childs');
		$counts = wpforo_forum($forum['forumid'], 'counts');
		$topics = WPF()->topic->get_topics( array("forumids" => $data, "orderby" => "type, modified", "order" => "DESC", "row_count" => wpforo_setting( 'forums', 'layout_extended_intro_topics_count' ) ) );

		$has_topics = is_array($topics) && !empty($topics);

		$forum_url = wpforo_forum($forum['forumid'], 'url');
		$topic_toglle = wpforo_setting( 'forums', 'layout_extended_intro_topics_toggle' );

		if( !empty($forum['icon']) ){
            $forum['icon'] = trim((string) $forum['icon']);
		    if( strpos((string) $forum['icon'], ' ') === false ) $forum['icon'] = 'fas ' . $forum['icon'];
        }
		$forum_icon = ( !empty($forum['icon']) ) ? $forum['icon'] : 'fas fa-comments';
		?>
	    <div id="wpf-forum-<?php echo $forum['forumid'] ?>" class="forum-wrap <?php wpforo_unread($forum['forumid'], 'forum') ?>">
	       <div class="wpforo-forum">
	         <div class="wpforo-forum-icon" style="<?php echo ( !is_rtl() ) ? 'border-left: 3px solid' : 'border-right: 3px solid'; ?> <?php echo esc_attr($forum['color']) ?>"><i class="<?php echo esc_attr($forum_icon) ?> wpfcl-0"></i></div>
	         <div class="wpforo-forum-info">
	            <h3 class="wpforo-forum-title"><a href="<?php echo esc_url((string) $forum_url) ?>"><?php echo esc_html($forum['title']); ?></a> <?php wpforo_viewing( $forum ); ?></h3>
	            <div class="wpforo-forum-description"><?php echo $forum['description']; ?></div>

	            <?php if($has_sub_forums) : ?>

	                <div class="wpforo-subforum">
	                   <ul>
	                    	<li class="first wpfcl-2"><?php wpforo_phrase('Subforums'); ?>:</li>
	                    	<?php foreach($sub_forums as $sub_forum) :
	                    		if( !WPF()->perm->forum_can( 'vf', $sub_forum['forumid'] ) ) continue;
                                if( !empty($sub_forum['icon']) ){
                                    $sub_forum['icon'] = trim((string) $sub_forum['icon']);
                                    if( strpos((string) $sub_forum['icon'], ' ') === false ) $sub_forum['icon'] = 'fas ' . $sub_forum['icon'];
                                }
								$sub_forum_icon = ( isset($sub_forum['icon']) && $sub_forum['icon']) ? $sub_forum['icon'] : 'fas fa-comments'; ?>
	                    		<li class="<?php wpforo_unread($sub_forum['forumid'], 'forum') ?>"><i class="<?php echo esc_attr($sub_forum_icon) ?> wpfcl-0"></i>&nbsp;<a href="<?php echo esc_url( (string) wpforo_forum($sub_forum['forumid'], 'url') ) ?>"><?php echo esc_html($sub_forum['title']); ?></a> <?php wpforo_viewing( $sub_forum ); ?></li>

	                    	<?php endforeach; ?>

	                   </ul>
	                   <br class="wpf-clear" />
	                </div><!-- wpforo-subforum -->

	            <?php endif; ?>

				<?php if($has_topics) : ?>

		            <div class="wpforo-forum-footer">
		                <span class="wpfcl-5"><?php wpforo_phrase('Recent Topics'); ?></span> &nbsp;
		                <i id="img-arrow-<?php echo intval($forum['forumid']) ?>" class="topictoggle fas fa-chevron-<?php echo ( $topic_toglle == 1 ? 'up' : 'down' ) ?>" style="color: rgb(67, 166, 223);font-size: 14px; cursor: pointer;"></i>
		            </div>

		        <?php endif ?>

	         </div><!-- wpforo-forum-info -->

	         <div class="wpforo-forum-stat-posts"><?php echo wpforo_print_number($counts['posts']) ?></div>
	         <div class="wpforo-forum-stat-topics"><?php echo wpforo_print_number($counts['topics']) ?></div>

	       </div><!-- wpforo-forum -->

			<?php if($has_topics) : ?>

	           <div class="wpforo-last-topics-<?php echo intval($forum['forumid']) ?>" style="display: <?php echo ( $topic_toglle == 1 ? 'block' : 'none' ) ?>;">
	              <div class="wpforo-last-topics-tab">&nbsp;</div>
	              <div class="wpforo-last-topics-list">
	                <ul>
						<?php foreach($topics as $topic) : ?>
                        	<?php $last_post = wpforo_post($topic['last_post']) ?>
							<?php $member = wpforo_member($last_post); ?>
                            <?php if(!empty($last_post) && !empty($member)): ?>
                                <li class="<?php wpforo_topic_types( $topic ); wpforo_unread( $topic['topicid'], 'topic') ?>">
                                    <div class="wpforo-last-topic-title">
                                        <?php wpforo_topic_title($topic, $last_post['url'], '{i}{p}{au}{tc}{/a}{n}', true, '', wpforo_setting( 'forums', 'layout_extended_intro_topics_length' )) ?>
                                    </div>
                                    <div class="wpforo-last-topic-avatar">
                                        <?php echo wpforo_user_avatar( $member, 30 ) ?>
                                    </div>
                                    <div class="wpforo-last-topic-user" title="<?php echo esc_attr($member['display_name']) ?>">
                                        <?php wpforo_member_link($member, 'by', 12); ?><br>
                                        <span class="wpforo-last-topic-date wpforo-date-ago"><?php wpforo_date($topic['modified']); ?></span>
                                    </div>
                                </li>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php if( intval($forum['topics']) > wpforo_setting( 'forums', 'layout_extended_intro_topics_count' ) ): ?>
                            <li>
                                <div class="wpf-vat">
                                	<a href="<?php echo esc_url((string) $forum_url) ?>"><?php wpforo_phrase('view all topics', true, 'lower');  ?> <i class="fas fa-angle-right" aria-hidden="true"></i></a>
                                </div>
                                <br class="wpf-clear" />
                            </li>
						<?php endif ?>
	                </ul>
	              </div>
	              <br class="wpf-clear" />
	           </div><!-- wpforo-last-topics -->

			<?php endif; ?>

	    </div><!-- forum-wrap -->

        <?php do_action( 'wpforo_loop_hook', $key, $forum ) ?>

	<?php endforeach; ?> <!-- $forums as $forum -->

	<?php if( !$forum_list ): ?>
		<?php do_action( 'wpforo_forum_loop_no_forums', $cat ); ?>
	<?php endif; ?>

</div><!-- wpfl-1 -->
