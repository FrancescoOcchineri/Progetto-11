<?php
// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-2">

    <div class="wpforo-topic-head">
        <div class="head-title"><?php wpforo_phrase( 'Topic Title' ) ?></div>
        <div class="head-stat-lastpost"><?php wpforo_phrase( 'Last Post' ) ?></div>
        <div class="head-stat-views"><?php wpforo_phrase( 'Views' ) ?></div>
        <div class="head-stat-posts"><?php wpforo_phrase( 'Posts' ) ?></div>
        <br class="wpf-clear">
    </div>

	<?php foreach( $topics as $key => $topic ) : ?>

		<?php
		$last_poster = [];
		$last_post   = [];
		$member      = wpforo_member( $topic );
		if( isset( $topic['last_post'] ) && $topic['last_post'] != 0 ) {
			$last_post   = wpforo_post( $topic['last_post'] );
			$last_poster = wpforo_member( $last_post );
		}
		$topic_url = wpforo_topic( $topic['topicid'], 'url' )
		?>

        <div class="topic-wrap <?php wpforo_unread( $topic['topicid'], 'topic' ); ?>">
            <div class="wpforo-topic">
				<?php if( WPF()->usergroup->can( 'va' ) && wpforo_setting( 'profiles', 'avatars' ) ): ?>
                    <div class="wpforo-topic-avatar"><?php echo wpforo_user_avatar( $member, 48 ) ?></div>
				<?php endif; ?>
                <div class="wpforo-topic-info">
                    <p class="wpforo-topic-title"><?php wpforo_topic_title( $topic, $topic_url, '{i}{p}{au}{t}{/a}{n}{v}' ) ?></p>
					<p class="wpforo-topic-start-info wpfcl-2"><?php wpforo_member_link( $member ); ?><span class="wpforo-date wpforo-date-ago">, <?php wpforo_date( $topic['created'] ); ?></span></p>
                    <div class="wpforo-topic-badges"><?php do_action( 'wpforo_topic_info_end', $topic ); ?></div>
                </div>
				<?php if( wpfval( $last_post, 'url' ) ) : ?>
                    <div class="wpforo-topic-stat-lastpost"><span><?php wpforo_member_link( $last_poster, 'by' ); ?> <a href="<?php echo esc_url( (string) $last_post['url'] ) ?>" title="<?php wpforo_phrase( 'View the latest post' ) ?>"><i class="fas fa-chevron-right fa-sx wpfcl-a"></i></a></span><br> <?php wpforo_date( wpfval( $last_post, 'created' ) ); ?></div>
				<?php else: ?>
                    <div class="wpforo-topic-stat-lastpost"><?php wpforo_phrase( 'Replies not found' ) ?></div>
				<?php endif; ?>
                <div class="wpforo-topic-stat-views"><?php echo intval( $topic['views'] ) ?></div>
                <div class="wpforo-topic-stat-posts"><?php echo intval( $topic['posts'] ) ?></div>
                <br class="wpf-clear">
            </div><!-- wpforo-topic -->
        </div><!-- topic-wrap -->

		<?php do_action( 'wpforo_loop_hook', $key ) ?>

	<?php endforeach; ?>
</div><!-- wpfl-2 -->
