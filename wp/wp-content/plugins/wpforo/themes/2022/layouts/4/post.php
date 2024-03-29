<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-4 wpforo-section">

    <div class="wpforo-post-head">
        <?php wpforo_topic_head($forum, $topic); ?>
	</div>

    <?php wpforo_moderation_tools(); ?>

    <?php wpforo_check_threads($posts); ?>

<?php foreach($posts as $key => $post) : ?>
        <?php $post = wpforo_post($post['postid']); $member = wpforo_member($post); $replies = WPF()->post->get_thread_tree($post); $reply_count = (wpfval($replies, 'count')) ? $replies['count'] : 0; ?>
        <div id="post-<?php echo wpforo_bigintval($post['postid']) ?>" data-postid="<?php echo wpforo_bigintval($post['postid']) ?>" data-userid="<?php echo wpforo_bigintval($member['userid']) ?>" data-mention="<?php echo esc_attr( ( wpforo_setting( 'profiles', 'mention_nicknames' ) ? $member['user_nicename'] : '') ) ?>" data-isowner="<?php echo esc_attr( (int) (bool) wpforo_is_owner($member['userid']) ) ?>" class="post-wrap reply-wrap wpfn-<?php echo ($key+1); ?><?php if( $post['is_first_post'] ) echo ' wpfp-first' ?>">
            <?php wpforo_share_toggle($post['url'], $post['body']); ?>
            <div class="wpforo-post wpf-parent-post wpfcl-1">
              <div class="wpf-left">
                  <?php if( WPF()->usergroup->can('va') && wpforo_setting( 'profiles', 'avatars' ) ): ?>
                      <div class="wpf-author-avatar"><?php echo wpforo_user_avatar( $member, 80 ) ?></div>
                  <?php endif ?>
                  <div class="wpf-author-data">
                      <?php wpforo_member_badge($member) ?>
                      <div class="wpf-author-posts"><?php wpforo_phrase('Posts') ?>: <?php echo intval($member['posts']) ?></div>
                  </div>
                  <div id="wpforo-memberinfo-toggle-<?php echo intval($post['postid']) ?>" class="wpforo-membertoggle" title="<?php wpforo_phrase('More') ?>">
                      <i class="fas fa-caret-down" aria-hidden="true"></i>
                  </div>
                  <div id="wpforo-memberinfo-<?php echo intval($post['postid']) ?>" class="wpforo-memberinfo">
                      <div class="wpf-member-profile-buttons">
                          <?php WPF()->tpl->member_buttons($member) ?>
                          <?php WPF()->tpl->member_social_buttons($member) ?>
                      </div>
                  </div>
              </div>
              <div class="wpf-right">
                  <div class="wpf-content-head">
                      <div class="wpf-content-head-top">
                          <div class="wpf-author">
                              <div class="wpf-author-name"><span><?php WPF()->member->show_online_indicator($member['userid']) ?></span>&nbsp;<?php wpforo_member_link($member); ?></div>
                              <div class="wpf-author-title"><?php wpforo_member_title($member, true, '', '', array('custom-fields', 'custom-title', 'rating-title')); ?></div>
	                          <?php wpforo_topic_starter($topic, $post) ?>
                          </div>
                          <div class="wpf-post-date"><?php wpforo_date($post['created'], 'M d, Y g:i a'); ?></div>
                      </div>
                      <div class="wpf-content-head-bottom">
                          <div class="wpf-author">
                            <?php wpforo_member_nicename($member, '@'); ?>
                            <div class="wpf-author-title"><?php wpforo_member_title($member,true, '', ', ', array('usergroup')) ?></div>
                            <div class="wpf-author-joined"><i class="fas fa-calendar-alt"></i> <?php wpforo_phrase('Joined') ?>: <?php wpforo_date($member['user_registered']); ?></div>
                          </div>
                          <div class="wpf-post-btns">
	                          <?php wpforo_post_buttons( 'icon', [ 'bookmark', 'report', 'link' ], $forum, $topic, $post ); ?>
	                          <?php wpforo_share_toggle($post['url'], $post['body'], 'top'); ?>
                          </div>
                      </div>
                  </div>
                  <div class="wpf-content">
                      <?php if($post['status']): ?>
                          <div class="wpf-mod">
                              <span class="wpf-mod-message"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> <?php wpforo_phrase('Awaiting moderation') ?></span>
                          </div>
                      <?php endif; ?>
                      <div class="wpforo-post-content">
                          <?php wpforo_content($post); ?>
                      </div>
                      <div class="wpforo-post-meta">
                          <?php do_action( 'wpforo_tpl_post_loop_after_content', $post, $member ) ?>
                          <?php wpforo_post_edited($post); ?>
                          <?php if( wpforo_setting( 'profiles', 'signature' ) ): ?>
                              <?php if($member['signature']): ?><div class="wpforo-post-signature"><?php wpforo_signature( $member ) ?></div><?php endif; ?>
                          <?php endif; ?>
                      </div>
                  </div>
                  <div class="wpf-content-foot">
	                  <?php do_action( 'wpforo_post_footer_start', $post, $topic, $forum, 4  ); ?>
                      <div class="wpf-reply">
                          <?php wpforo_post_buttons( 'icon-text', 'reply', $forum, $topic, $post ); ?>
                      </div>
                      <div class="wpf-buttons">
	                      <?php wpforo_post_buttons( 'icon', [ 'quote', 'approved', 'edit', 'delete' ], $forum, $topic, $post ); ?>
                      </div>
                  </div>
                  <div class="wpforo-post-footer">
	                  <?php do_action( 'wpforo_post_footer_bottom_start', $post, $topic, $forum, 4 ); ?>
                  </div>
                  <div class="wpforo-portable-form-wrap"></div>
              </div>
	      </div><!-- wpforo-post -->
            <?php if( $post['is_first_post'] ): ?>
                <div class="wpforo-topic-meta">
                    <?php wpforo_tags( $topic, true, 'small'); ?>
                </div>
                <?php if( $topic['posts'] > 1 ): ?>
                    <script type="text/javascript">jQuery(document).ready(function($){ if($('#wpf-ttgg-<?php echo intval($post['postid']) ?>').length){ $('#wpf-replies-sep').hide(); } });</script>
                    <div id="wpf-replies-sep" class="wpf-replies-sep">
                        <div class="wpf-replies-title"><?php $replies_count = '<span class="wpf-replies-count">' . ($topic['posts'] - 1) . '</span>' ?>
                            <i class="far fa-comment"></i> <?php ( (int) $topic['posts'] > 2 ) ? printf( wpforo_phrase('%s Replies', false), $replies_count ) : printf( wpforo_phrase('%s Reply', false), $replies_count ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if( $reply_count && wpforo_setting( 'topics', 'layout_threaded_nesting_level' ) ): ?>
                <div class="wpf-post-replies-bar" id="wpf-ttgg-<?php echo intval($post['postid']); ?>">
                    <div class="wpf-post-replies-info">
                        <i class="far fa-comments"></i>
                        <span class="wpf-post-replies-title"><?php ( (int) $reply_count > 1 ) ? printf( wpforo_phrase('%s Replies', false), wpforo_print_number($reply_count) ) : printf( wpforo_phrase('%s Reply', false), wpforo_print_number($reply_count) ); ?></span>
                    </div>
                    <div class="wpf-prsep"></div>
                    <div wpf-tooltip="<?php wpforo_phrase('Hide replies') ?>" class="wpforo-ttgg"><i class="fas fa-angle-up"></i></div>
                </div>
            <?php endif; ?>
	 	</div><!-- post-wrap -->

        <?php WPF()->post->replies( $replies, $topic, $forum ); ?>

	 	<?php do_action( 'wpforo_loop_hook', $key ) ?>

	<?php endforeach; ?>

</div><!-- wpfl-1 -->
