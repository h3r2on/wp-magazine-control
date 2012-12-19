wp-magazine-control
===================

a version control system for wordpress magazines

Usage
-----

Before activating the plugin add mic_issue and the version number as a custom field on each post. 

On activation the plugin will set the homepage to use only posts from the version set in the admin section. This theme uses the `pre_get_posts()` function to alter The Loop query. Some custom themes will break this. If this plugin does not work out of the box you'll need to add this manually before the `query_posts($args)` call(s) depending on how complex your theme is.

    $current_issue = get_option('mic_current_issue');
    if (defined('MIC_ISSUE')) {
      $metaq = array(
        array(
          'key' => 'mic_issue',
          'value' => MIC_ISSUE,
          'compare' => '='
        )
      );
    } else {
      $metaq = array(
        array(
          'key' => 'mic_issue',
          'value' => $current_issue,
          'compare' => '='
        )
      );
    }
    $args['meta_query'] = $metaq;


Allows for a beta switch to test the next version this requires the Edit Flow plugin to add a custom post status until this [bug is fixed](http://core.trac.wordpress.org/ticket/12706)

TODO:
-----

allow custom set versions to persist past the main page.