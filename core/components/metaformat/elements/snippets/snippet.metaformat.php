<?php
/**
 * MetaFormat
 *
 * @package metaformat
 *
 * DESCRIPTION
 *
 * MetaFormat generates the tags within the HEAD of an HTML resource 
 *
 * PROPERTIES:
 *
 * See properties tab
 *
 * USAGE:
 *
  [[MetaFormat]]
 *  
  [[MetaFormat?
    &legacyMode=`IE=edge,chrome=1`
    &siteLive=`0`
    &keywords=`[[TaggerGetTags? &resources=`[[*id]]` &rowTpl=`itemTagRaw` &separator=`,` ]]`
    &viewport=`width=device-width, initial-scale=1, user-scalable=1`
    &css=`//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css, 27`
    &js=`//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js, 16`
    &social=`MetaFormatOpenGraph, MetaFormatTwitter`
    &icon=`metaIcon`
    &socialImage=`/assets/myImageURL.jpg`
  ]]
 *
 */

$mf = $modx->getService(  'metaformat',
                          'MetaFormat',
                          $modx->getOption('metaformat.core_path', null, $modx->getOption('core_path').'components/metaformat/').'model/metaformat/'
                        );
                          
if (!($mf instanceof MetaFormat)) return '';

return $mf->getMetaFormat($scriptProperties);