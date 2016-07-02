<?php

/**
 * Default Language file for GooglePix
 *
 * @package googlepix
 * @subpackage lexicon
 */
 
$_lang['metaformat'] = 'MetaFormat';

//properties
$_lang['prop_metaformat.id_desc'] = 'The ID of the resource. If omitted, defaults to current resource.';
$_lang['prop_metaformat.titleItem_desc'] = 'Resource field from which to generate the TITLE tag from. Either <var>pagetitle</var>, <var>longtitle</var>, <var>menutitle</var> or <var>description</var>.';
$_lang['prop_metaformat.titleMax_desc'] = 'Maximum number of items to appear in the TITLE tag.';
$_lang['prop_metaformat.titleSep_desc'] = 'Separator to appear between TITLE tag components.';
$_lang['prop_metaformat.titleHomeSuffix_desc'] = 'String to append to the TITLE tag of the home page.';
$_lang['prop_metaformat.charset_desc'] = 'Page encoding. If omitted defaults to the MODX setting modx_charset.';
$_lang['prop_metaformat.base_desc'] = 'Base URL. If omitted defaults to the MODX setting site_url.';
$_lang['prop_metaformat.siteLive_desc'] = 'If site is not live (0) then all pages will have their robots meta-tag set to the <code>robotsNoIndex</code> value.';
$_lang['prop_metaformat.hostMedia_desc'] = 'The host to prefix all media links with, such as style-sheets and short-cut icon. Useful if you use a separate domain for serving media files.';
$_lang['prop_metaformat.hostCode_desc'] = 'The host to prefix all scripts with. Useful if you use a separate domain for serving JavaScript files.';
$_lang['prop_metaformat.hostAll_desc'] = 'The host to prefix all media links and scripts with. Overrides hostMedia and hostCode.';
$_lang['prop_metaformat.css_desc'] = 'Comma-separated list of resource ids or paths of style-sheets, to be inserted as LINK tags. Each path automatically prefixed with <code>hostMedia</code> or <code>hostAll</code>, if set';
$_lang['prop_metaformat.js_desc'] = 'Comma-separated list of resource ids or paths of JavaScript files, to be inserted as LINK tags. Each path automatically prefixed with <code>hostCode</code> or <code>hostAll</code>, if set';
$_lang['prop_metaformat.rss_desc'] = 'Comma-separated list of resource ids or paths of RSS files, to be inserted as LINK tags.';
$_lang['prop_metaformat.rssType_desc'] = 'RSS mime-type used in RSS LINK tags.';
$_lang['prop_metaformat.preMain_desc'] = 'Comma-separated list of chunk names to be inserted before MetaFormat output.';
$_lang['prop_metaformat.postMain_desc'] = 'Comma-separated list of chunk names to be inserted after MetaFormat output.';
$_lang['prop_metaformat.preCSS_desc'] = 'Comma-separated list of chunk names to be inserted before CSS LINK tags.';
$_lang['prop_metaformat.postCSS_desc'] = 'Comma-separated list of chunk names to be inserted after CSS LINK tags.';
$_lang['prop_metaformat.preJS_desc'] = 'Comma-separated list of chunk names to be inserted before SCRIPT tags.';
$_lang['prop_metaformat.postJS_desc'] = 'Comma-separated list of chunk names to be inserted after SCRIPT tags.';
$_lang['prop_metaformat.icon_desc'] = 'Name of chunk containing icon tags. Useful if you have a lot of icon tags.';
$_lang['prop_metaformat.favicon_desc'] = 'Path to favicon. Path automatically prefixed with <code>hostCode</code> or <code>hostAll</code>.';
$_lang['prop_metaformat.robotsIndex_desc'] = 'The robots META tag to apply to a resource when their searchable property set to true.';
$_lang['prop_metaformat.robotsNoIndex_desc'] = 'The robots META tag to apply to a resource when their searchable property set to false.';
$_lang['prop_metaformat.keywords_desc'] = 'Comma-separated list of keywords.';
$_lang['prop_metaformat.keywordsItem_desc'] = 'Resource field or template variable from which to generate the META keywords tag from. Either <var>pagetitle</var>, <var>longtitle</var>, <var>menutitle</var>, <var>description</var>, or template variable prefixed with <var>tv.</var>';
$_lang['prop_metaformat.descriptionItem_desc'] = 'Resource field or template variable from which to generate the META keywords tag from. Either <var>pagetitle</var>, <var>longtitle</var>, <var>menutitle</var>, <var>description</var>, or template variable prefixed with <var>tv.</var>';
$_lang['prop_metaformat.descriptionMaxLength_desc'] = 'The maximum length the description is to be truncated to.';
$_lang['prop_metaformat.authorItem_desc'] = 'Resource field from which to generate the META author tag from. Either <var>createdby</var>, <var>editedby</var> or <var>publishedby</var>.';
$_lang['prop_metaformat.legacyMode_desc'] = 'Internet Explorer <a href="https://msdn.microsoft.com/en-us/library/jj676915(v=vs.85).aspx" title="Specifying legacy document modes">legacy mode</a>, e.g. <var>IE=edge,chrome=1</var>. However, preferred method is to <a href="http://stackoverflow.com/questions/6771258/whats-the-difference-if-meta-http-equiv-x-ua-compatible-content-ie-edge-e" title="">apply custom headers</a> on the web-server.';
$_lang['prop_metaformat.viewport_desc'] = 'Layout control setting for <a href="https://developer.mozilla.org/en/docs/Mozilla/Mobile/Viewport_meta_tag" title="Using the viewport meta tag to control layout on mobile browsers">mobile browsers</a>, e.g. <var>width=device-width, initial-scale=1, user-scalable=1</var>';
$_lang['prop_metaformat.social_desc'] = 'Comma-separated list of chunk names, which contain social tags like Open Graph. Several chunk properties are made available to these chunks, such as <code>socialImage</code>, <code>socialTwitter</code> and <code>socialFacebook</code>.';
$_lang['prop_metaformat.socialItems_desc'] = 'JSON formatted string specifying the properties to pass to <code>social</code> as placeholders.';
$_lang['prop_metaformat.excludeBase_desc'] = 'Whether to exclude the BASE tag.';
